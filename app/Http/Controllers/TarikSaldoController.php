<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TarikSaldo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TarikSaldoController extends Controller
{
    public function index(Request $request)
    {

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $nasabahId = $request->nasabah_id;

        $query = TarikSaldo::with('nasabah')->latest();

        if ($request->filled('nasabah')) {
            $query->whereHas('nasabah', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nasabah . '%');
            });
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
        }

        if ($nasabahId) {
            $query->where('nasabah_id', $nasabahId);
        }

        if ($request->action == 'cetak') {
            $tarikSaldos = $query->get();
            $pdf = Pdf::loadView('pages.transaksi.tarik-saldo.laporan_pdf', compact('tarikSaldos', 'tanggalAwal', 'tanggalAkhir', 'nasabahId'))
                ->setPaper('A4', 'portrait');

            $tanggal = now()->format('d-m-y');

            $namaFile = 'laporan-tarik-saldo-' . $tanggal . '.pdf';

            return $pdf->download($namaFile);
        }

        // Default tampil data
        $tarikSaldos = $query->paginate(10);
        $nasabahs = Nasabah::all();
        return view('pages.transaksi.tarik-saldo.index', compact('tarikSaldos', 'nasabahs'));
    }

    public function create()
    {
        // Tampilkan nasabah yang mempunyai saldo > 0
        $nasabahs = Nasabah::where('saldo', '>', 0)->get();
        return view('pages.transaksi.tarik-saldo.create', compact('nasabahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'jumlah_tarik' => 'required|numeric|min:1',
        ]);

        $nasabah = Nasabah::findOrFail($request->nasabah_id);

        if ($nasabah->saldo < $request->jumlah_tarik) {
            return back()->with('error', 'Saldo nasabah tidak mencukupi');
        } elseif ($request->jumlah_tarik <= 0) {
            return back()->with('error', 'Jumlah tarik harus lebih besar dari 0');
        } elseif ($request->jumlah_tarik > $nasabah->saldo) {
            return back()->with('error', 'Jumlah tarik tidak boleh melebihi saldo nasabah');
        } elseif ($request->jumlah_tarik > 1000000) {
            return back()->with('error', 'Jumlah tarik tidak boleh melebihi 1.000.000');
        } elseif ($request->jumlah_tarik < 10000) {
            return back()->with('error', 'Jumlah tarik tidak boleh kurang dari 10.000');
        }

        // Kurangi saldo
        $nasabah->decrement('saldo', $request->jumlah_tarik);

        // Simpan transaksi tarik saldo
        TarikSaldo::create([
            'nasabah_id' => $request->nasabah_id,
            'jumlah_tarik' => $request->jumlah_tarik,
        ]);

        return redirect()->route('tarik-saldo.index')->with('success', 'Tarik saldo berhasil');
    }
}
