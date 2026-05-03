<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setoran;
use App\Models\TarikSaldo;

class TransaksiController extends Controller
{
    public function index()
    {
        $nasabahId = Auth::user()->nasabah->id;
        
        $setorans = Setoran::where('nasabah_id', $nasabahId)->latest()->get()->map(function($item) {
            $item->tipe = 'Setoran';
            $item->jumlah = $item->total_harga; // Assuming this field exists in Setoran
            return $item;
        });
        
        $penarikans = TarikSaldo::where('nasabah_id', $nasabahId)->latest()->get()->map(function($item) {
            $item->tipe = 'Penarikan';
            $item->jumlah = $item->jumlah_tarik;
            return $item;
        });
        
        $transaksi = $setorans->concat($penarikans)->sortByDesc('created_at');
        
        return view('nasabah.riwayat-transaksi', compact('transaksi'));
    }
}
