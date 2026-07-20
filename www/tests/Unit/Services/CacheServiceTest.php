<?php

namespace Tests\Unit\Services;

use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Pengepul;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Cache::flush();
        $this->cacheService = $this->app->make(CacheService::class);

        $this->seed();
    }

    public function test_get_dashboard_summary(): void
    {
        $summary = $this->cacheService->getDashboardSummary();

        $this->assertArrayHasKey('jumlah_nasabah', $summary);
        $this->assertArrayHasKey('jumlah_pengepul', $summary);
        $this->assertArrayHasKey('jumlah_sampah', $summary);
        $this->assertArrayHasKey('total_sampah_disetorkan', $summary);
        $this->assertArrayHasKey('total_tabungan_nasabah', $summary);
        $this->assertArrayHasKey('total_penjualan_sampah', $summary);
        $this->assertArrayHasKey('total_penjualan', $summary);
    }

    public function test_invalidate_dashboard_clears_summary(): void
    {
        $this->cacheService->getDashboardSummary();
        $this->assertTrue(Cache::has('dashboard:summary'));

        $this->cacheService->invalidateDashboard();

        $this->assertFalse(Cache::has('dashboard:summary'));
    }

    public function test_get_nasabah_terbaru(): void
    {
        $result = $this->cacheService->getNasabahTerbaru();
        $this->assertNotNull($result);
    }
}
