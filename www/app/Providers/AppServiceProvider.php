<?php

namespace App\Providers;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Setoran;
use App\Observers\JenisSampahObserver;
use App\Observers\NasabahObserver;
use App\Observers\SetoranObserver;
use App\Services\ElasticsearchService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ElasticsearchService::class, function () {
            return new ElasticsearchService();
        });
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Nasabah::observe(NasabahObserver::class);
        JenisSampah::observe(JenisSampahObserver::class);
        Setoran::observe(SetoranObserver::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ElasticIndexCreate::class,
                \App\Console\Commands\ElasticIndexDelete::class,
                \App\Console\Commands\ElasticIndexRecreate::class,
                \App\Console\Commands\ElasticIndexList::class,
                \App\Console\Commands\ElasticImportNasabah::class,
                \App\Console\Commands\ElasticImportSampah::class,
                \App\Console\Commands\ElasticImportSetoran::class,
            ]);
        }
    }
}
