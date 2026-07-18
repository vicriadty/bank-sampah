<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticIndexRecreate extends Command
{
    protected $signature = 'elastic:index:recreate {index?}';
    protected $description = 'Recreate Elasticsearch indexes (delete + create)';

    public function handle(ElasticsearchService $es): int
    {
        $this->call('elastic:index:delete', [
            'index' => $this->argument('index'),
            '--force' => true,
        ]);

        $this->call('elastic:index:create', [
            'index' => $this->argument('index'),
        ]);

        $this->info('Index recreation completed.');

        return Command::SUCCESS;
    }
}
