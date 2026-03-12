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

    // public function index(Request $request)
    // {
    //     $query = TarikSaldo::with('nasabah')->latest();

    //     // Filter berdasarkan nama nasabah
    //     if ($request->filled('nasabah')) {
    //         $query->whereHas('nasabah', function ($q) use ($request) {
    //             $q->where('nama', 'like', '%' . $request->nasabah . '%');
    //         });
    //     }

    //     // Filter berdasarkan range tanggal
    //     if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
    //         $query->whereBetween('created_at', [
    //             $request->tanggal_mulai . ' 00:00:00',
    //             $request->tanggal_selesai . ' 23:59:59'
    //         ]);
    //     }

    //     $tarikSaldos = $query->get();

    //     return view('pages.transaksi.tarik-saldo.index', compact('tarikSaldos'));
    // }


    public function create()
    {
        $nasabahs = Nasabah::all();
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
        } else if ($request->jumlah_tarik < 10000) {
            return back()->with('error', 'Tarik saldo minimal 10.000');
        } else if ($request->jumlah_tarik > 1000000) {
            return back()->with('error', 'Tarik saldo maksimal 1.000.000');
        } else
            // Kurangi saldo
            $nasabah->decrement('saldo', $request->jumlah_tarik);

        // Simpan transaksi tarik saldo
        TarikSaldo::create([
            'nasabah_id' => $request->nasabah_id,
            'jumlah_tarik' => $request->jumlah_tarik,
        ]);

        return redirect()->route('tarik-saldo.index')->with('success', 'Tarik saldo berhasil');
    }

    // public function laporanPDF()
    // {
    //     $tarikSaldos = TarikSaldo::with('nasabah')->latest()->get();

    //     $pdf = Pdf::loadView('pages.transaksi.tarik-saldo.laporan_pdf', compact('tarikSaldos'));

    //     $tanggal = now()->format('d-m-y');

    //     $namaFile = 'laporan-tarik-saldo-' . $tanggal . '.pdf';

    //     return $pdf->download($namaFile);
    // }
}
