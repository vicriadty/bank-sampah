<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DetailPenjualanSampah;
use App\Models\DompetNasabah;
use App\Models\RiwayatKonversiEmas;
use App\Models\Nasabah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\JenisSampah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService)
    {
        // Summary cards
        $jumlahNasabah = Nasabah::count();
        $jumlahPengepul = Pengepul::count();
        $jumlahSampah = JenisSampah::count();
        $totalSampahDisetorkan = SetoranDetail::sum('berat');
        $totalTabunganNasabah = DompetNasabah::sum('saldo_rupiah');
        $totalPenjualanSampah = DetailPenjualanSampah::sum('berat');
        $totalPenjualan = PenjualanSampah::sum('total_harga');

        // Gold stats
        $goldPrice = $goldPriceService->getPrice();
        $totalRupiahDiKonversi = RiwayatKonversiEmas::sum('saldo_terpakai');
        $totalGoldExchanged = RiwayatKonversiEmas::sum('jumlah_gram');

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

        // Chart: Komposisi kategori sampah
        $komposisiSampah = SetoranDetail::join('jenis_sampahs', 'setoran_details.sampah_id', '=', 'jenis_sampahs.id')
            ->join('kategori_sampahs', 'jenis_sampahs.kategori_id', '=', 'kategori_sampahs.id')
            ->select('kategori_sampahs.nama_kategori', DB::raw('SUM(setoran_details.berat) as total_berat'))
            ->groupBy('kategori_sampahs.nama_kategori')
            ->get();

        // Chart: Penukaran emas per bulan
        $goldPerBulan = RiwayatKonversiEmas::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
            DB::raw('SUM(jumlah_gram) as total_gram'),
            DB::raw('SUM(saldo_terpakai) as total_saldo')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Nasabah terbaru
        $nasabahTerbaru = Nasabah::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'jumlahNasabah',
            'jumlahPengepul',
            'jumlahSampah',
            'totalSampahDisetorkan',
            'totalTabunganNasabah',
            'totalPenjualanSampah',
            'totalPenjualan',
            'goldPrice',
            'totalRupiahDiKonversi',
            'totalGoldExchanged',
            'setoranPerBulan',
            'nasabahBaruPerBulan',
            'komposisiSampah',
            'goldPerBulan',
            'nasabahTerbaru'
        ));
    }
}
