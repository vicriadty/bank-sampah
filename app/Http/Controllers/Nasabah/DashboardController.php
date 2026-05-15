<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Models\GoldExchange;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Auth;
use App\Models\Setoran;
use App\Models\TarikSaldo;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService)
    {
        $user = Auth::user()->load('nasabah');
        $nasabah = $user->nasabah;
        
        // Stats
        $totalSetoran = Setoran::where('nasabah_id', $nasabah->id)->count();
        $totalTarik = TarikSaldo::where('nasabah_id', $nasabah->id)->where('status', 'approved')->count();
        $totalGoldGrams = $nasabah->totalGoldGrams();

        // Gold price
        $goldPrice = $goldPriceService->getPrice();

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

        // Chart: Penarikan per bulan
        $penarikanPerBulan = TarikSaldo::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bulan"),
                DB::raw('SUM(jumlah_tarik) as total')
            )
            ->where('nasabah_id', $nasabah->id)
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return view('nasabah.dashboard', compact(
            'user', 'nasabah', 'totalSetoran', 'totalTarik',
            'totalGoldGrams', 'goldPrice',
            'setoranPerBulan', 'penarikanPerBulan'
        ));
    }

    public function infoSaldo()
    {
        $user = Auth::user()->load('nasabah');
        $nasabah = $user->nasabah;
        
        $setorans = Setoran::where('nasabah_id', $nasabah->id)->latest()->get();
        $penarikans = TarikSaldo::where('nasabah_id', $nasabah->id)->latest()->get();
        
        return view('nasabah.info-saldo', compact('nasabah', 'setorans', 'penarikans'));
    }
}
