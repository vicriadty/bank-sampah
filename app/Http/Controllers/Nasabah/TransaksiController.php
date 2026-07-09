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

        $transaksi = Setoran::where('nasabah_id', $nasabahId)->latest()->get()->map(function ($item) {
            $item->tipe = 'Setoran';
            $item->jumlah = $item->total_harga;
            $item->status = $item->status;
            $item->keterangan = $item->alasan_batal ?? '-';
            return $item;
        });

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
