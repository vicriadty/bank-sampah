<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticIndexDelete extends Command
{
    protected $signature = 'elastic:index:delete {index?} {--force}';
    protected $description = 'Delete Elasticsearch indexes';

    public function handle(ElasticsearchService $es): int
    {
        $indexName = $this->argument('index');

        if (!$indexName) {
            if ($this->option('force')) {
                $indexes = ['nasabahs', 'jenis_sampahs', 'setorans'];
            } else {
                if (!$this->confirm('Delete ALL indexes? This cannot be undone.')) {
                    return Command::SUCCESS;
                }
                $indexes = ['nasabahs', 'jenis_sampahs', 'setorans'];
            }
        } else {
            $indexes = [$indexName];
        }

        foreach ($indexes as $name) {
            if ($es->deleteIndex($name)) {
                $this->info("Index '{$name}' deleted.");
            } else {
                $this->warn("Index '{$name}' not found or failed to delete.");
            }
        }

        return Command::SUCCESS;
    }
}
