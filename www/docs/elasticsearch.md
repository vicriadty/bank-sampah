# Elasticsearch Integration

## Architecture

```
                User
                  |
                  v
         Search Controller (API)
                  |
                  v
      Search Repository Layer
                  |
                  v
      Elasticsearch Service
          |             |
          v             v
 Elasticsearch    MySQL (Fallback)
```

MySQL remains the **primary database** (source of truth). Elasticsearch is used only as a search engine.

## Docker Setup

Elasticsearch 8.11.1 runs in Docker with security disabled:

```yaml
elasticsearch:
  image: docker.elastic.co/elasticsearch/elasticsearch:8.11.1
  environment:
    - discovery.type=single-node
    - xpack.security.enabled=false
    - ES_JAVA_OPTS=-Xms512m -Xmx512m
```

## Configuration

Environment variables in `.env`:

```
ELASTICSEARCH_HOST=http://elasticsearch:9200
ELASTICSEARCH_INDEX_PREFIX=banksampah
ELASTICSEARCH_TIMEOUT=2
```

Configuration file: `config/elasticsearch.php`

## Commands

### Index Management

```bash
# Create all indexes (nasabahs, jenis_sampahs, setorans)
php artisan elastic:index:create

# Create a specific index
php artisan elastic:index:create nasabahs

# Delete all indexes
php artisan elastic:index:delete --force

# Delete a specific index
php artisan elastic:index:delete nasabahs

# Recreate (delete + create)
php artisan elastic:index:recreate

# List indexes with document count
php artisan elastic:index:list
```

### Import Data

```bash
php artisan elastic:import:nasabah
php artisan elastic:import:sampah
php artisan elastic:import:setoran
```

Import reads all MySQL records and bulk-inserts into Elasticsearch.

## Indexes & Mapping

### nasabahs

| Field | Type | Searchable |
|---|---|---|
| id | integer | - |
| nik | text (ngram) | Yes |
| nama | text (ngram) | Yes |
| email | keyword | Yes |
| alamat | text (ngram) | Yes |
| no_hp | keyword | - |
| created_at | date | - |

### jenis_sampahs

| Field | Type | Searchable |
|---|---|---|
| id | integer | - |
| nama_jenis | text (ngram) | Yes |
| kategori | text (ngram) | Yes |
| harga_per_kg | float | - |
| stok | float | - |

### setorans

| Field | Type | Searchable |
|---|---|---|
| id | integer | - |
| kode_setoran | keyword | Yes |
| nasabah_id | integer | - |
| nasabah | text (ngram) | Yes |
| total_harga | float | - |
| status | keyword | Yes |
| created_at | date | - |

All text indexes use ngram analyzer (min_gram=2, max_gram=10) for partial matching.

## Synchronization

Observers automatically sync data to Elasticsearch:

```
NasabahObserver  → index/update/delete nasabahs
JenisSampahObserver → index/update/delete jenis_sampahs
SetoranObserver  → index/update/delete setorans
```

Flow:
```
Save to MySQL (source of truth)
       |
       v (if success)
Update Elasticsearch
       |
       v
Return response
```

Observers are registered in `AppServiceProvider::boot()`.

## Search API

Endpoints (require authentication):

```bash
GET /search/nasabah?q=keyword&page=1&per_page=10
GET /search/sampah?q=keyword&page=1&per_page=10
GET /search/setoran?q=keyword&page=1&per_page=10
```

Returns JSON:
```json
{
  "data": [...],
  "total": 10,
  "per_page": 10,
  "current_page": 1,
  "last_page": 1
}
```

## Fallback Mechanism

If Elasticsearch is unavailable, the application automatically falls back to MySQL LIKE:

```
Try Elasticsearch
       |
       v (exception)
Log error
       |
       v
Fallback MySQL LIKE
       |
       v
Return results
```

## Logging

Elasticsearch operations are logged to `storage/logs/elasticsearch-YYYY-MM-DD.log`:
- Connection success/failure
- Index creation/deletion
- Document indexing/updating/deleting
- Search errors
- Bulk import results

## Testing

```bash
# Run all tests
php artisan test

# Specific tests
php artisan test --filter ElasticsearchTest
```

## Troubleshooting

### Connection refused
Ensure Elasticsearch container is running:
```bash
docker ps | grep elasticsearch
```

### Index already exists
```bash
php artisan elastic:index:recreate
```

### Search returns no results
1. Check indexes: `php artisan elastic:index:list`
2. Re-import: `php artisan elastic:import:nasabah`
3. Check logs: `storage/logs/elasticsearch-*.log`

### Compatible-with error
Ensure `elasticsearch/elasticsearch` package is version ^8.x:
```bash
composer show elasticsearch/elasticsearch
```

## Future Improvements

- [ ] Replace admin search views with Elasticsearch
- [ ] Add autocomplete suggestions
- [ ] Add search highlighting
- [ ] Implement more advanced filters (range, aggregation)
- [ ] Add reindex command for data migration
- [ ] Implement index lifecycle management
