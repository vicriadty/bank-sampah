<?php

namespace App\Jobs;

use App\Models\Nasabah;
use App\Services\ElasticsearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportNasabahJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        private array $nasabahIds
    ) {}

    public function handle(ElasticsearchService $elasticsearch): void
    {
        $nasabahs = Nasabah::whereIn('id', $this->nasabahIds)->get();

        foreach ($nasabahs as $nasabah) {
            try {
                $elasticsearch->indexDocument('nasabahs', (string) $nasabah->id, [
                    'id' => $nasabah->id,
                    'nik' => $nasabah->nik,
                    'nama' => $nasabah->nama,
                    'email' => $nasabah->email,
                    'alamat' => $nasabah->alamat,
                    'no_hp' => $nasabah->no_hp,
                    'created_at' => $nasabah->created_at?->toIso8601String(),
                ]);
            } catch (\Exception $e) {
                Log::error('ImportNasabahJob failed for nasabah', [
                    'id' => $nasabah->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
