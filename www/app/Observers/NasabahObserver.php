<?php

namespace App\Observers;

use App\Models\Nasabah;
use App\Services\ElasticsearchService;

class NasabahObserver
{
    public function __construct(
        private ElasticsearchService $elasticsearch
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
    }

    public function deleted(Nasabah $nasabah): void
    {
        $this->elasticsearch->deleteDocument('nasabahs', (string) $nasabah->id);
    }
}
