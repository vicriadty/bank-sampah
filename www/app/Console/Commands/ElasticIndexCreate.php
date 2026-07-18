<?php

namespace App\Console\Commands;

use App\Services\ElasticsearchService;
use Illuminate\Console\Command;

class ElasticIndexCreate extends Command
{
    protected $signature = 'elastic:index:create {index?}';
    protected $description = 'Create Elasticsearch indexes';

    public function handle(ElasticsearchService $es): int
    {
        $indexName = $this->argument('index');

        $indexes = $indexName ? [$indexName] : array_keys($this->getMappings());

        foreach ($indexes as $name) {
            $mappings = $this->getMappings()[$name] ?? null;
            if (!$mappings) {
                $this->error("Unknown index: {$name}");
                continue;
            }

            if ($es->createIndex($name, $mappings['mappings'], $mappings['settings'])) {
                $this->info("Index '{$name}' created successfully.");
            } else {
                $this->warn("Index '{$name}' already exists or failed to create.");
            }
        }

        return Command::SUCCESS;
    }

    private function getMappings(): array
    {
        $commonSettings = [
            'index.max_ngram_diff' => 10,
        ];

        $analyzer = [
            'analysis' => [
                'analyzer' => [
                    'ngram_analyzer' => [
                        'tokenizer' => 'ngram_tokenizer',
                    ],
                ],
                'tokenizer' => [
                    'ngram_tokenizer' => [
                        'type' => 'ngram',
                        'min_gram' => 2,
                        'max_gram' => 10,
                        'token_chars' => ['letter', 'digit'],
                    ],
                ],
            ],
        ];

        return [
            'nasabahs' => [
                'settings' => array_merge($commonSettings, $analyzer),
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'nik' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'nama' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'email' => ['type' => 'keyword'],
                        'alamat' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'no_hp' => ['type' => 'keyword'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
            'jenis_sampahs' => [
                'settings' => array_merge($commonSettings, $analyzer),
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'nama_jenis' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'kategori' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'harga_per_kg' => ['type' => 'float'],
                        'stok' => ['type' => 'float'],
                    ],
                ],
            ],
            'setorans' => [
                'settings' => array_merge($commonSettings, $analyzer),
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'kode_setoran' => ['type' => 'keyword'],
                        'nasabah_id' => ['type' => 'integer'],
                        'nasabah' => ['type' => 'text', 'analyzer' => 'ngram_analyzer'],
                        'total_harga' => ['type' => 'float'],
                        'status' => ['type' => 'keyword'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ];
    }
}
