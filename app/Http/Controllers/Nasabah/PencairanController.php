<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TarikSaldo;
use App\Models\Nasabah;

class PencairanController extends Controller
{
    public function create()
    {
        $nasabah = Auth::user()->nasabah;
        return view('nasabah.request-pencairan', compact('nasabah'));
    }

    public function store(Request $request)
    {
        $nasabah = Auth::user()->nasabah;
        
        $request->validate([
            'jumlah_tarik' => 'required|numeric|min:10000|max:' . $nasabah->saldo,
        ], [
            'jumlah_tarik.max' => 'Saldo tidak mencukupi untuk jumlah penarikan ini.',
            'jumlah_tarik.min' => 'Minimal penarikan adalah Rp 10.000.',
        ]);

        TarikSaldo::create([
            'nasabah_id' => $nasabah->id,
            'jumlah_tarik' => $request->jumlah_tarik,
            'status' => 'pending',
            'keterangan' => $request->keterangan ?? 'Permintaan pencairan oleh nasabah',
        ]);

        return redirect()->route('nasabah.riwayat-transaksi')->with('success', 'Permintaan pencairan berhasil dikirim dan sedang menunggu persetujuan admin.');
    }
}
