<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatKonversiEmas;
use App\Models\Setoran;

class TransaksiController extends Controller
{
    public function index()
    {
        $nasabahId = Auth::user()->nasabah->id;

        $transaksi = Setoran::with('details.sampah')
            ->where('nasabah_id', $nasabahId)
            ->latest()
            ->get();

        return view('nasabah.riwayat-transaksi', compact('transaksi'));
    }

    public function riwayatKonversi()
    {
        $nasabahId = Auth::user()->nasabah->id;

        $konversi = RiwayatKonversiEmas::where('nasabah_id', $nasabahId)
            ->latest()
            ->get();

        return view('nasabah.riwayat-konversi', compact('konversi'));
    }
}
