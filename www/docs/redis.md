# Redis Integration

## Architecture

```
                    User
                      │
                      ▼
                Laravel Controller
                      │
                      ▼
                Service Layer
          ┌───────────┴───────────┐
          ▼                       ▼
    RedisService            CacheService
          │                       │
          └───────────┬───────────┘
                      ▼
                    Redis
          │            │            │
          ▼            ▼            ▼
       Cache       Session       Queue
                      │
                      ▼
                   MySQL
```

## Docker Configuration

- **Image**: `redis:alpine`
- **Container**: `laravel_redis`
- **Port**: 6379
- **Network**: bridges to `laravel_app`, `laravel_db`, `elasticsearch_spp`

### docker-compose.yml

```yaml
redis:
  image: redis:alpine
  container_name: laravel_redis
  ports:
    - "6379:6379"
  networks:
    - banksampah-network
  healthcheck:
    test: ["CMD", "redis-cli", "ping"]
    interval: 10s
    timeout: 5s
    retries: 5
```

### Dockerfile

```dockerfile
RUN pecl install redis && docker-php-ext-enable redis
```

## Redis Configuration

### Environment (.env)

```env
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### config/database.php

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', '0'),
    ],
    'cache' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
],
```

## Services

### RedisService (`app/Services/RedisService.php`)

Reusable Redis abstraction. All Redis access must go through this service.

| Method | Description |
|--------|-------------|
| `get(key, default)` | Get value by key |
| `put(key, value, ttl)` | Store value with TTL |
| `remember(key, ttl, callback)` | Cache with callback |
| `forget(key)` | Delete key |
| `increment(key, amount)` | Increment value |
| `decrement(key, amount)` | Decrement value |
| `exists(key)` | Check key exists |
| `flush()` | Flush current database |
| `keys(pattern)` | List keys matching pattern |
| `info()` | Redis server info/stats |

### CacheService (`app/Services/CacheService.php`)

High-level cache management for application-specific data.

| Method | Description | TTL |
|--------|-------------|-----|
| `getDashboardSummary()` | Cache dashboard summary stats | 10 min |
| `getGoldStats()` | Cache gold conversion stats | 10 min |
| `getSetoranPerBulan()` | Monthly deposit chart data | 10 min |
| `getNasabahBaruPerBulan()` | New members chart data | 10 min |
| `getKomposisiSampah()` | Waste composition chart | 10 min |
| `getGoldPerBulan()` | Gold exchange chart | 10 min |
| `getNasabahTerbaru()` | Latest members | 10 min |
| `invalidateDashboard()` | Clear all dashboard caches | - |
| `invalidateGoldStats()` | Clear gold-related caches | - |

## Cache Strategy

| Cache Key | TTL | Invalidated By |
|-----------|-----|----------------|
| `dashboard:*` | 10 minutes | NasabahObserver, SetoranObserver, JenisSampahObserver, PenjualanObserver |
| `gold_price_*` | 30 minutes | GoldPriceService::clearCache() |
| `search:*` | 5 minutes | TTL expiry |

## Queue Strategy

Queue uses Redis as backend (`QUEUE_CONNECTION=redis`).

Available Jobs:

| Job | Description | Timeout |
|-----|-------------|---------|
| `ConvertGoldBalanceJob` | Auto-convert saldo to gold | 300s |
| `GenerateReportJob` | Generate PDF reports | 120s |
| `ImportNasabahJob` | Batch import nasabah to ES | 300s |
| `SyncElasticsearchJob` | Sync single document to ES | 120s |

Run worker:

```bash
php artisan queue:work redis --sleep=3 --tries=3
```

## Gold Price Cache

Gold prices are cached for 30 minutes (1800s) via `RedisService::remember()`.

Flow:

```
Cron / Request
      ↓
Check Redis (gold_price_XAU_IDR)
      ↓
Cache Exists?
      ↓
YES → Return cached price
      ↓
NO  → Request MetalPriceAPI
      ↓
      Save to Redis (TTL 1800s)
      ↓
      Return price
```

## Monitoring Commands

### redis:status

Display Redis server status and statistics:

```
php artisan redis:status
```

Output:
- Server version
- Uptime
- Memory usage
- Connected clients
- Hit/miss rate

### redis:flush-cache

Flush all Redis cache (with confirmation):

```
php artisan redis:flush-cache
```

### redis:cache-report

List all cache keys grouped by prefix:

```
php artisan redis:cache-report
```

## Logging

Redis logs to `storage/logs/redis.log` (daily rotation, 14 days retention).

Logged events:
- Cache miss/hit
- Queue failures
- Connection errors
- Timeouts
- Reconnections

## Observers

All CRUD operations on these models trigger dashboard cache invalidation:

| Observer | Model | Cache Invalidation |
|----------|-------|-------------------|
| NasabahObserver | Nasabah | Dashboard |
| SetoranObserver | Setoran | Dashboard |
| JenisSampahObserver | JenisSampah | Dashboard |
| PenjualanObserver | PenjualanSampah | Dashboard |

## Testing

```bash
# Unit tests for services
php artisan test --filter="RedisServiceTest"
php artisan test --filter="CacheServiceTest"
php artisan test --filter="GoldPriceServiceTest"
```

## Troubleshooting

### Redis extension not found

```bash
# Check if redis extension is loaded
php -m | grep redis

# Install via PECL
pecl install redis
docker-php-ext-enable redis
```

### Connection refused

```bash
# Check if Redis container is running
docker ps | grep redis

# Check connectivity from app container
docker exec laravel_app php -r '$r = new Redis(); $r->connect("redis", 6379); echo $r->ping();'
```

### Cache not working

```bash
# Verify CACHE_STORE is set to redis
php artisan tinker --execute="echo config('cache.default');"

# Test Redis connectivity
php artisan redis:status

# Clear all cache
php artisan redis:flush-cache
```

## Future Improvements

- Redis Cluster support for horizontal scaling
- Redis Sentinel for high availability
- Cache warming on application startup
- Rate limiting using Redis
- Real-time notifications via Redis Pub/Sub
- Session locking for concurrent request handling
