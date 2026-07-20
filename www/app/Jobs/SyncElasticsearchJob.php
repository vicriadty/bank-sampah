<?php

namespace App\Jobs;

use App\Services\ElasticsearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncElasticsearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        private string $index,
        private string $id,
        private array $data,
        private string $action = 'index'
    ) {}

    public function handle(ElasticsearchService $elasticsearch): void
    {
        try {
            match ($this->action) {
                'index' => $elasticsearch->indexDocument($this->index, $this->id, $this->data),
                'update' => $elasticsearch->updateDocument($this->index, $this->id, $this->data),
                'delete' => $elasticsearch->deleteDocument($this->index, $this->id),
                default => Log::warning('SyncElasticsearchJob: unknown action', ['action' => $this->action]),
            };
        } catch (\Exception $e) {
            Log::error('SyncElasticsearchJob failed', [
                'index' => $this->index,
                'id' => $this->id,
                'action' => $this->action,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
