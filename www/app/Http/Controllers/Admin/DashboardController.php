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
        $nasabahBaruPerBulan = $cacheService->getNasabahBaruPerBulan();
        $komposisiSampah = $cacheService->getKomposisiSampah();
        $goldPerBulan = $cacheService->getGoldPerBulan();
        $nasabahTerbaru = $cacheService->getNasabahTerbaru();

        return view('admin.dashboard', compact(
            'goldPrice',
            'setoranPerBulan',
            'nasabahBaruPerBulan',
            'komposisiSampah',
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
