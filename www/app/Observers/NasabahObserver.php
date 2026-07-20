<?php

namespace App\Observers;

use App\Models\Nasabah;
use App\Services\CacheService;
use App\Services\ElasticsearchService;

class NasabahObserver
{
    public function __construct(
        private ElasticsearchService $elasticsearch,
        private CacheService $cacheService
    ) {}

    public function created(Nasabah $nasabah): void
    {
        $this->elasticsearch->indexDocument('nasabahs', (string) $nasabah->id, [
            'id' => $nasabah->id,
            'nik' => $nasabah->nik,
            'nama' => $nasabah->nama,
            'email' => $nasabah->email,
            'alamat' => $nasabah->alamat,
            'no_hp' => $nasabah->no_hp,
            'created_at' => $nasabah->created_at?->toIso8601String(),
        ]);

        $this->cacheService->invalidateDashboard();
    }

    public function updated(Nasabah $nasabah): void
    {
        $this->elasticsearch->updateDocument('nasabahs', (string) $nasabah->id, [
            'nik' => $nasabah->nik,
            'nama' => $nasabah->nama,
            'email' => $nasabah->email,
            'alamat' => $nasabah->alamat,
            'no_hp' => $nasabah->no_hp,
        ]);

        $this->cacheService->invalidateDashboard();
    }

    public function deleted(Nasabah $nasabah): void
    {
        $this->elasticsearch->deleteDocument('nasabahs', (string) $nasabah->id);
        $this->cacheService->invalidateDashboard();
    }
}
