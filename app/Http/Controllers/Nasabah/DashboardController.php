<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Services\GoldPriceService;
use Illuminate\Support\Facades\Auth;
use App\Models\Setoran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(GoldPriceService $goldPriceService)
    {
        $user = Auth::user()->load('nasabah');
        $nasabah = $user->nasabah;
        
        // Stats
        $totalSetoran = Setoran::where('nasabah_id', $nasabah->id)->count();
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

        return view('nasabah.dashboard', compact(
            'user', 'nasabah', 'totalSetoran',
            'totalGoldGrams', 'goldPrice',
            'setoranPerBulan'
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
