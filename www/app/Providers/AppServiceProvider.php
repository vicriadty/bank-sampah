<?php

namespace App\Providers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\PenjualanSampah;
use App\Models\Setoran;
use App\Observers\JenisSampahObserver;
use App\Observers\NasabahObserver;
use App\Observers\PenjualanObserver;
use App\Observers\SetoranObserver;
use App\Services\CacheService;
use App\Services\ElasticsearchService;
use App\Services\RedisService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ElasticsearchService::class, function () {
            return new ElasticsearchService();
        });

        $this->app->singleton(RedisService::class, function () {
            return new RedisService();
        });

        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService($app->make(RedisService::class));
        });
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Nasabah::observe(NasabahObserver::class);
        JenisSampah::observe(JenisSampahObserver::class);
        Setoran::observe(SetoranObserver::class);
        PenjualanSampah::observe(PenjualanObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ElasticIndexCreate::class,
                \App\Console\Commands\ElasticIndexDelete::class,
                \App\Console\Commands\ElasticIndexRecreate::class,
                \App\Console\Commands\ElasticIndexList::class,
                \App\Console\Commands\ElasticImportNasabah::class,
                \App\Console\Commands\ElasticImportSampah::class,
                \App\Console\Commands\ElasticImportSetoran::class,
                \App\Console\Commands\RedisStatus::class,
                \App\Console\Commands\RedisFlushCache::class,
                \App\Console\Commands\RedisCacheReport::class,
            ]);
        }
    }
}
