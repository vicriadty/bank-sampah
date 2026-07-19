<?php

namespace App\Observers;

use App\Models\Setoran;
use App\Services\ElasticsearchService;

class SetoranObserver
{
    public function __construct(
        private ElasticsearchService $elasticsearch
    ) {}

    public function created(Setoran $setoran): void
    {
        $setoran->loadMissing('nasabah');

        $this->elasticsearch->indexDocument('setorans', (string) $setoran->id, [
            'id' => $setoran->id,
            'kode_setoran' => $setoran->kode_setoran,
            'nasabah_id' => $setoran->nasabah_id,
            'nasabah' => $setoran->nasabah?->nama ?? '',
            'total_harga' => (float) $setoran->total_harga,
            'status' => $setoran->status,
            'created_at' => $setoran->created_at?->toIso8601String(),
        ]);
    }

    public function updated(Setoran $setoran): void
    {
        $setoran->loadMissing('nasabah');

        $this->elasticsearch->updateDocument('setorans', (string) $setoran->id, [
            'nasabah_id' => $setoran->nasabah_id,
            'nasabah' => $setoran->nasabah?->nama ?? '',
            'total_harga' => (float) $setoran->total_harga,
            'status' => $setoran->status,
        ]);
    }

    public function deleted(Setoran $setoran): void
    {
        $this->elasticsearch->deleteDocument('setorans', (string) $setoran->id);
    }
}
