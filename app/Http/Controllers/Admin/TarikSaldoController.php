<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

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
        $status = $request->status;

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

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->action == 'cetak') {
            $tarikSaldos = $query->get();
            $pdf = Pdf::loadView('admin.transaksi.tarik-saldo.laporan_pdf', compact('tarikSaldos', 'tanggalAwal', 'tanggalAkhir', 'nasabahId'))
                ->setPaper('A4', 'portrait');

            $tanggal = now()->format('d-m-y');

            $namaFile = 'laporan-tarik-saldo-' . $tanggal . '.pdf';

            return $pdf->download($namaFile);
        }

        // Default tampil data
        $tarikSaldos = $query->paginate(10);
        $nasabahs = Nasabah::all();
        return view('admin.transaksi.tarik-saldo.index', compact('tarikSaldos', 'nasabahs'));
    }

    public function create()
    {
        // Tampilkan nasabah yang mempunyai saldo > 0
        $nasabahs = Nasabah::where('saldo', '>', 0)->get();
        return view('admin.transaksi.tarik-saldo.create', compact('nasabahs'));
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
        }

        // Simpan transaksi tarik saldo (Admin langsung approved)
        TarikSaldo::create([
            'nasabah_id' => $request->nasabah_id,
            'jumlah_tarik' => $request->jumlah_tarik,
            'status' => 'approved',
            'keterangan' => 'Penarikan dicatat oleh admin'
        ]);

        // Kurangi saldo
        $nasabah->decrement('saldo', $request->jumlah_tarik);

        return redirect()->route('admin.tarik-saldo.index')->with('success', 'Tarik saldo berhasil');
    }

    public function approve($id)
    {
        $tarikSaldo = TarikSaldo::findOrFail($id);
        
        if ($tarikSaldo->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $nasabah = $tarikSaldo->nasabah;

        if ($nasabah->saldo < $tarikSaldo->jumlah_tarik) {
            $tarikSaldo->update(['status' => 'rejected', 'keterangan' => 'Ditolak otomatis karena saldo tidak mencukupi']);
            return back()->with('error', 'Saldo nasabah tidak mencukupi. Transaksi ditolak otomatis.');
        }

        $nasabah->decrement('saldo', $tarikSaldo->jumlah_tarik);
        $tarikSaldo->update(['status' => 'approved']);

        return back()->with('success', 'Permintaan pencairan disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $tarikSaldo = TarikSaldo::findOrFail($id);
        
        if ($tarikSaldo->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses.');
        }

        $tarikSaldo->update([
            'status' => 'rejected',
            'keterangan' => $request->keterangan ?? 'Ditolak oleh admin'
        ]);

        return back()->with('success', 'Permintaan pencairan ditolak.');
    }
}
