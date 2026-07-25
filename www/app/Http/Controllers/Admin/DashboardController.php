<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\GoldPriceService;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService, CacheService $cacheService)
    {
        $summary = $cacheService->getDashboardSummary();
        $goldStats = $cacheService->getGoldStats();

        $goldPrice = $goldPriceService->getPrice();
        $setoranPerBulan = $cacheService->getSetoranPerBulan();
        $komposisiSampah = $cacheService->getKomposisiSampah();
        $goldPriceHistory = $cacheService->getGoldPriceHistory();
        $nasabahBaruPerBulan = $cacheService->getNasabahBaruPerBulan();
        $goldPerBulan = $cacheService->getGoldPerBulan();
        $nasabahTerbaru = $cacheService->getNasabahTerbaru();

        $goldChangePercent = null;
        if ($goldPriceHistory->count() >= 2) {
            $last = $goldPriceHistory->last()->harga_rata;
            $prev = $goldPriceHistory->slice(-2, 1)->first()->harga_rata;
            $goldChangePercent = $prev > 0 ? round((($last - $prev) / $prev) * 100, 2) : 0;
        }

        $goldHistoryCategories = $goldPriceHistory->pluck('tanggal')->toArray();
        $goldHistoryData = $goldPriceHistory->pluck('harga_rata')->map(fn($v) => (float) $v)->toArray();

        return view('admin.dashboard', compact(
            'goldPrice',
            'setoranPerBulan',
            'komposisiSampah',
            'goldPriceHistory',
            'goldHistoryCategories',
            'goldHistoryData',
            'goldChangePercent',
            'nasabahBaruPerBulan',
            'goldPerBulan',
            'nasabahTerbaru'
        ) + [
            'jumlahNasabah' => $summary['jumlah_nasabah'],
            'jumlahPengepul' => $summary['jumlah_pengepul'],
            'jumlahSampah' => $summary['jumlah_sampah'],
            'totalSampahDisetorkan' => $summary['total_sampah_disetorkan'],
            'totalTabunganNasabah' => $summary['total_tabungan_nasabah'],
            'totalPenjualanSampah' => $summary['total_penjualan_sampah'],
            'totalPenjualan' => $summary['total_penjualan'],
            'totalRupiahDiKonversi' => $goldStats['total_rupiah_dikonversi'],
            'totalGoldExchanged' => $goldStats['total_gold_exchanged'],
        ]);
    }
}