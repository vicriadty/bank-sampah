<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\RiwayatKonversiEmas;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Auth;
use App\Models\Setoran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService)
    {
        $user = Auth::user()->load('nasabah.dompet');
        $nasabah = $user->nasabah;
        
        // Stats
        $totalSetoran = Setoran::where('nasabah_id', $nasabah->id)->count();
        $saldoAktif = $nasabah->dompet->saldo_rupiah ?? 0;
        $saldoEmas = $nasabah->dompet->saldo_emas_gram ?? 0;
        $saldoDiKonversi = RiwayatKonversiEmas::where('nasabah_id', $nasabah->id)->sum('saldo_terpakai');

        // Gold price
        $goldPrice = $goldPriceService->getPrice();

        // Transaksi terbaru
        $transaksiTerbaru = Setoran::with('details.sampah')
            ->where('nasabah_id', $nasabah->id)
            ->latest()
            ->take(5)
            ->get();

        // Chart: Setoran per bulan (6 bulan terakhir)
        $setoranPerBulan = Setoran::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('nasabah_id', $nasabah->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Chart: Konversi per bulan
        $konversiPerBulan = RiwayatKonversiEmas::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(saldo_terpakai) as total_rupiah'),
                DB::raw('SUM(jumlah_gram) as total_gram')
            )
            ->where('nasabah_id', $nasabah->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('nasabah.dashboard', compact(
            'user', 'nasabah',
            'saldoAktif', 'saldoEmas', 'saldoDiKonversi', 'totalSetoran',
            'goldPrice',
            'setoranPerBulan', 'konversiPerBulan',
            'transaksiTerbaru'
        ));
    }

    public function infoSaldo()
    {
        $user = Auth::user()->load('nasabah');
        $nasabah = $user->nasabah;
        
        $setorans = Setoran::where('nasabah_id', $nasabah->id)->latest()->get();
        
        return view('nasabah.info-saldo', compact('nasabah', 'setorans'));
    }
}
