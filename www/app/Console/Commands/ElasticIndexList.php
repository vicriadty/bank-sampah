<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticIndexList extends Command
{
    protected $signature = 'elastic:index:list';
    protected $description = 'List all Elasticsearch indexes';

    public function handle(ElasticsearchService $es): int
    {
        try {
            $response = $es->client()->cat()->indices(['format' => 'json']);
            $indexes = $response->asArray();

            if (empty($indexes)) {
                $this->info('No indexes found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($indexes as $idx) {
                if (str_starts_with($idx['index'], $es->indexPrefix)) {
                    $rows[] = [
                        $idx['index'],
                        $idx['docs.count'] ?? '0',
                        $idx['store.size'] ?? '0',
                        $idx['health'] ?? '?',
                    ];
                }
            }

            if (empty($rows)) {
                $this->info("No indexes with prefix '{$es->indexPrefix}' found.");
                return Command::SUCCESS;
            }

            $this->table(['Index', 'Docs', 'Size', 'Health'], $rows);
        } catch (\Exception $e) {
            $this->error('Failed to list indexes: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
