<?php

namespace App\Observers;

use App\Models\JenisSampah;
use App\Services\CacheService;
use App\Services\ElasticsearchService;

class JenisSampahObserver
{
    public function __construct(
        private ElasticsearchService $elasticsearch,
        private CacheService $cacheService
    ) {}

    public function created(JenisSampah $sampah): void
    {
        $sampah->loadMissing('kategoriSampah');

        $this->elasticsearch->indexDocument('jenis_sampahs', (string) $sampah->id, [
            'id' => $sampah->id,
            'nama_jenis' => $sampah->nama_jenis,
            'kategori' => $sampah->kategoriSampah?->nama_kategori ?? '',
            'harga_per_kg' => (float) $sampah->harga_per_kg,
            'stok' => (float) $sampah->stok,
        ]);

        $this->cacheService->invalidateDashboard();
    }

    public function updated(JenisSampah $sampah): void
    {
        $sampah->loadMissing('kategoriSampah');

        $this->elasticsearch->updateDocument('jenis_sampahs', (string) $sampah->id, [
            'nama_jenis' => $sampah->nama_jenis,
            'kategori' => $sampah->kategoriSampah?->nama_kategori ?? '',
            'harga_per_kg' => (float) $sampah->harga_per_kg,
            'stok' => (float) $sampah->stok,
        ]);

        $this->cacheService->invalidateDashboard();
    }

    public function deleted(JenisSampah $sampah): void
    {
        $this->elasticsearch->deleteDocument('jenis_sampahs', (string) $sampah->id);
        $this->cacheService->invalidateDashboard();
    }
}
