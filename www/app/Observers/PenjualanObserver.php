<?php

namespace App\Observers;

use App\Models\PenjualanSampah;
use App\Services\CacheService;

class PenjualanObserver
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    public function created(PenjualanSampah $penjualan): void
    {
        $this->cacheService->invalidateDashboard();
    }

    public function updated(PenjualanSampah $penjualan): void
    {
        $this->cacheService->invalidateDashboard();
    }

    public function deleted(PenjualanSampah $penjualan): void
    {
        $this->cacheService->invalidateDashboard();
    }
}
