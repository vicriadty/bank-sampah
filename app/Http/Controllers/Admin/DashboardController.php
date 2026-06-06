<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DetailPenjualanSampah;
use App\Models\GoldExchange;
use App\Models\Nasabah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService)
    {
        // Summary cards (existing)
        $jumlahNasabah = Nasabah::count();
        $jumlahPengepul = Pengepul::count();
        $totalSampahDisetorkan = SetoranDetail::sum('berat');
        $totalTabunganNasabah = Nasabah::sum('saldo');
        $totalPenjualanSampah = DetailPenjualanSampah::sum('berat');
        $totalPenjualan = PenjualanSampah::sum('total_harga');

        // Gold stats
        $goldPrice = $goldPriceService->getPrice();
        $totalGoldExchanged = GoldExchange::where('status', 'completed')->sum('jumlah_gram');
        $pendingGoldExchanges = GoldExchange::where('status', 'pending')->count();

        // Chart: Setoran per bulan (6 bulan terakhir)
        $setoranPerBulan = Setoran::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Chart: Nasabah baru per bulan
        $nasabahBaruPerBulan = Nasabah::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Chart: Komposisi jenis sampah
        $komposisiSampah = SetoranDetail::join('sampahs', 'setoran_details.sampah_id', '=', 'sampahs.id')
            ->join('jenis_sampahs', 'sampahs.jenis_sampah_id', '=', 'jenis_sampahs.id')
            ->select('jenis_sampahs.nama_jenis', DB::raw('SUM(setoran_details.berat) as total_berat'))
            ->groupBy('jenis_sampahs.nama_jenis')
            ->get();

        // Chart: Penukaran emas per bulan
        $goldPerBulan = GoldExchange::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(jumlah_gram) as total_gram'),
                DB::raw('SUM(jumlah_saldo) as total_saldo')
            )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('admin.dashboard', compact(
            'jumlahNasabah',
            'jumlahPengepul',
            'totalSampahDisetorkan',
            'totalTabunganNasabah',
            'totalPenjualanSampah',
            'totalPenjualan',
            'goldPrice',
            'totalGoldExchanged',
            'pendingGoldExchanges',
            'setoranPerBulan',
            'nasabahBaruPerBulan',
            'komposisiSampah',
            'goldPerBulan'
        ));
    }
}
