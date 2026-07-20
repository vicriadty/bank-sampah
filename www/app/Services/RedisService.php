<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisService
{
    private string $logChannel = 'redis';

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            $value = Cache::get($key);
            if ($value === null) {
                Log::channel($this->logChannel)->debug('Cache miss', ['key' => $key]);
                return $default;
            }
            Log::channel($this->logChannel)->debug('Cache hit', ['key' => $key]);
            return $value;
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis get error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }

    public function put(string $key, mixed $value, int $ttlSeconds = 600): void
    {
        try {
            Cache::put($key, $value, $ttlSeconds);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis put error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function remember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        try {
            return Cache::remember($key, $ttlSeconds, $callback);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis remember error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return $callback();
        }
    }

    public function forget(string $key): void
    {
        try {
            Cache::forget($key);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis forget error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function increment(string $key, int $value = 1): void
    {
        try {
            Cache::increment($key, $value);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis increment error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function decrement(string $key, int $value = 1): void
    {
        try {
            Cache::decrement($key, $value);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis decrement error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function exists(string $key): bool
    {
        try {
            return Cache::has($key);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis exists error', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function flush(): void
    {
        try {
            Redis::connection()->command('flushdb');
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis flush error', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function keys(string $pattern = '*'): array
    {
        try {
            $allKeys = Redis::connection()->command('keys', [$pattern]);
            if (!is_array($allKeys)) {
                return [];
            }
            $prefix = config('database.redis.options.prefix', '');
            return array_map(function ($k) use ($prefix) {
                return str_starts_with($k, $prefix) ? substr($k, strlen($prefix)) : $k;
            }, $allKeys);
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis keys error', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function info(): array
    {
        try {
            $raw = Redis::connection()->command('info');
            if (!is_array($raw)) {
                return [];
            }

            $dbSize = 0;
            if (isset($raw['db0']) && is_string($raw['db0'])) {
                preg_match('/keys=(\d+)/', $raw['db0'], $m);
                $dbSize = isset($m[1]) ? (int) $m[1] : 0;
            }

            return [
                'uptime_in_seconds' => (int) ($raw['uptime_in_seconds'] ?? 0),
                'used_memory_human' => $raw['used_memory_human'] ?? 'N/A',
                'used_memory_peak_human' => $raw['used_memory_peak_human'] ?? 'N/A',
                'total_connections_received' => (int) ($raw['total_connections_received'] ?? 0),
                'total_commands_processed' => (int) ($raw['total_commands_processed'] ?? 0),
                'keyspace_hits' => (int) ($raw['keyspace_hits'] ?? 0),
                'keyspace_misses' => (int) ($raw['keyspace_misses'] ?? 0),
                'connected_clients' => (int) ($raw['connected_clients'] ?? 0),
                'db_size' => $dbSize,
                'server_version' => $raw['redis_version'] ?? 'N/A',
            ];
        } catch (\Exception $e) {
            Log::channel($this->logChannel)->error('Redis info error', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
