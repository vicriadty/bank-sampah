<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\DetailPenjualanSampah;
use Illuminate\Http\Request;
use App\Models\Nasabah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use App\Models\TarikSaldo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahNasabah = Nasabah::count();
        $jumlahPengepul = Pengepul::count();
        $totalSampahDisetorkan = SetoranDetail::sum('berat');
        $totalTabunganNasabah = Nasabah::sum('saldo');
        $totalSaldoDitarik = TarikSaldo::sum('jumlah_tarik');
        $totalPenjualanSampah = DetailPenjualanSampah::sum('berat');
        $totalPenjualan = PenjualanSampah::sum('total_harga');

        return view('admin.dashboard', compact(
            'jumlahNasabah',
            'jumlahPengepul',
            'totalSampahDisetorkan',
            'totalTabunganNasabah',
            'totalSaldoDitarik',
            'totalPenjualanSampah',
            'totalPenjualan'
        ));
    }
}
