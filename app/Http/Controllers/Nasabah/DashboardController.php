<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setoran;
use App\Models\TarikSaldo;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('nasabah');
        $nasabah = $user->nasabah;
        
        // Stats
        $totalSetoran = Setoran::where('nasabah_id', $nasabah->id)->count();
        $totalTarik = TarikSaldo::where('nasabah_id', $nasabah->id)->where('status', 'approved')->count();
        
        return view('nasabah.dashboard', compact('user', 'nasabah', 'totalSetoran', 'totalTarik'));
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
