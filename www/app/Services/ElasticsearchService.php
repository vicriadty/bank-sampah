<?php

namespace App\Services;

use App\Exceptions\SearchUnavailableException;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Exception;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected ?Client $client = null;
    public string $indexPrefix;

    public function __construct()
    {
        $this->indexPrefix = config('elasticsearch.index_prefix', 'banksampah');
    }

    public function client(): Client
    {
        if ($this->client === null) {
            $host = config('elasticsearch.host', 'http://elasticsearch:9200');
            $timeout = config('elasticsearch.timeout', 2);

            $this->client = ClientBuilder::create()
                ->setHosts([$host])
                ->setRetries(0)
                ->setHttpClientOptions(['timeout' => $timeout])
                ->build();
        }
        return $this->client;
    }

    public function indexName(string $name): string
    {
        return $this->indexPrefix . '_' . $name;
    }

    public function isAvailable(): bool
    {
        try {
            $this->client()->info();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createIndex(string $name, array $mappings = [], array $settings = []): bool
    {
        $index = $this->indexName($name);
        try {
            $exists = $this->indexExists($name);
            if ($exists) {
                Log::channel('elasticsearch')->warning("Index already exists: {$index}");
                return false;
            }

            $params = ['index' => $index];
            if (!empty($settings)) {
                $params['body']['settings'] = $settings;
            }
            if (!empty($mappings)) {
                $params['body']['mappings'] = $mappings;
            }

            $this->client()->indices()->create($params);
            Log::channel('elasticsearch')->info("Index created: {$index}");
            return true;
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Failed to create index {$index}: " . $e->getMessage());
            return false;
        }
    }

    public function deleteIndex(string $name): bool
    {
        $index = $this->indexName($name);
        try {
            $exists = $this->indexExists($name);
            if (!$exists) {
                Log::channel('elasticsearch')->warning("Index not found: {$index}");
                return false;
            }

            $this->client()->indices()->delete(['index' => $index]);
            Log::channel('elasticsearch')->info("Index deleted: {$index}");
            return true;
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Failed to delete index {$index}: " . $e->getMessage());
            return false;
        }
    }

    public function indexExists(string $name): bool
    {
        $index = $this->indexName($name);
        try {
            $response = $this->client()->indices()->exists(['index' => $index]);
            return $response->getStatusCode() === 200;
        } catch (ClientResponseException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function indexDocument(string $name, string $id, array $body): bool
    {
        $index = $this->indexName($name);
        try {
            $this->client()->index([
                'index' => $index,
                'id' => $id,
                'body' => $body,
            ]);
            Log::channel('elasticsearch')->info("Document indexed: {$index}/{$id}");
            return true;
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Failed to index document {$index}/{$id}: " . $e->getMessage());
            return false;
        }
    }

    public function updateDocument(string $name, string $id, array $body): bool
    {
        $index = $this->indexName($name);
        try {
            $this->client()->update([
                'index' => $index,
                'id' => $id,
                'body' => ['doc' => $body],
            ]);
            Log::channel('elasticsearch')->info("Document updated: {$index}/{$id}");
            return true;
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Failed to update document {$index}/{$id}: " . $e->getMessage());
            return false;
        }
    }

    public function deleteDocument(string $name, string $id): bool
    {
        $index = $this->indexName($name);
        try {
            $this->client()->delete([
                'index' => $index,
                'id' => $id,
            ]);
            Log::channel('elasticsearch')->info("Document deleted: {$index}/{$id}");
            return true;
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Failed to delete document {$index}/{$id}: " . $e->getMessage());
            return false;
        }
    }

    public function bulkIndex(string $name, array $documents): array
    {
        $index = $this->indexName($name);
        $body = [];
        $success = 0;
        $failed = 0;

        foreach ($documents as $doc) {
            $id = $doc['id'] ?? null;
            $body[] = ['index' => ['_index' => $index, '_id' => (string) $id]];
            $body[] = $doc;
        }

        try {
            $response = $this->client()->bulk(['body' => $body]);
            $responseData = $response->asArray();

            if (isset($responseData['items'])) {
                foreach ($responseData['items'] as $item) {
                    if (isset($item['index']['error'])) {
                        $failed++;
                        Log::channel('elasticsearch')->error('Bulk index failed: ' . json_encode($item['index']['error']));
                    } else {
                        $success++;
                    }
                }
            }

            Log::channel('elasticsearch')->info("Bulk indexed {$success} documents, {$failed} failed (index: {$index})");
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Bulk index failed for {$index}: " . $e->getMessage());
            $failed = count($documents);
        }

        return ['success' => $success, 'failed' => $failed];
    }

    public function search(string $name, array $query, int $from = 0, int $size = 10): array
    {
        $index = $this->indexName($name);
        try {
            $params = [
                'index' => $index,
                'body' => $query,
                'from' => $from,
                'size' => $size,
            ];

            $response = $this->client()->search($params);
            $data = $response->asArray();

            $hits = $data['hits']['hits'] ?? [];
            $total = $data['hits']['total']['value'] ?? 0;

            $results = array_map(fn($hit) => array_merge(
                ['id' => (int) ($hit['_id'] ?? 0)],
                $hit['_source'] ?? []
            ), $hits);

            return [
                'results' => $results,
                'total' => $total,
                'from' => $from,
                'size' => $size,
            ];
        } catch (NoNodeAvailableException | ClientResponseException | ServerResponseException $e) {
            Log::channel('elasticsearch')->error("Search failed for {$index}: " . $e->getMessage());
            throw new SearchUnavailableException("Elasticsearch unavailable: " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            Log::channel('elasticsearch')->error("Unexpected search error for {$index}: " . $e->getMessage());
            throw new SearchUnavailableException("Elasticsearch error: " . $e->getMessage(), 0, $e);
        }
    }
}
