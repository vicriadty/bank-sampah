<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\DompetNasabah;
use App\Models\KategoriSampah;
use App\Models\Nasabah;
use App\Models\JenisSampah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetoranController extends Controller
{
    public function index(Request $request)
    {

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $nasabahId = $request->nasabah_id;

        $query = Setoran::with(['nasabah', 'details.sampah'])->latest();

        if ($request->filled('nasabah')) {
            $search = $request->nasabah;
            $query->where(function ($q) use ($search) {
                $q->whereHas('nasabah', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })->orWhere('kode_setoran', 'like', '%' . $search . '%');
            });
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereDate('created_at', '>=', $tanggalAwal)
                  ->whereDate('created_at', '<=', $tanggalAkhir);
        }

        if ($nasabahId) {
            $query->where('nasabah_id', $nasabahId);
        }

        if ($request->action == 'cetak') {
            $setorans = $query->get();
            $pdf = Pdf::loadView('admin.transaksi.setor-sampah.laporan_pdf', compact('setorans', 'tanggalAwal', 'tanggalAkhir', 'nasabahId'))
                ->setPaper('A4', 'portrait');

            $tanggal = now()->format('d-m-y');

            $namaFile = 'laporan-setoran-' . $tanggal . '.pdf';

            return $pdf->download($namaFile);
        }

        // Default tampil data
        $setorans = $query->paginate(10);
        $nasabahs = Nasabah::all();
        $kategoriSampah = KategoriSampah::all();
        $jenisSampahs = JenisSampah::with(['kategoriSampah'])
            ->select('id', 'nama_jenis', 'kategori_id', 'harga_per_kg', 'stok')
            ->get();

        $hasFilter = $request->filled('nasabah') || $request->filled('tanggal_awal');
        $hasData = $setorans->total() > 0;

        return view('admin.transaksi.setor-sampah.index', compact('setorans', 'nasabahs', 'kategoriSampah', 'jenisSampahs', 'hasFilter', 'hasData'));
    }

    public function create()
    {
        $nasabahs = Nasabah::all();
        $kategoriSampah = KategoriSampah::all();
        $jenisSampahs = JenisSampah::with(['kategoriSampah'])
            ->select('id', 'nama_jenis', 'kategori_id', 'harga_per_kg', 'stok')
            ->get();
        return view('admin.transaksi.setor-sampah.create', compact('nasabahs', 'kategoriSampah', 'jenisSampahs'));
    }

    public function getSampahByJenis($id)
    {
        $jenisSampahs = JenisSampah::where('kategori_id', $id)->get();
        return response()->json($jenisSampahs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => ['required', 'exists:nasabahs,id'],
            'sampah_id' => ['required', 'array'],
            'sampah_id.*' => ['required', 'exists:jenis_sampahs,id'],
            'berat' => ['required', 'array'],
            'berat.*' => ['required', 'numeric', 'min:0.1'],
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;

            $setoran = Setoran::create([
                'nasabah_id' => $request->nasabah_id,
                'total_harga' => 0,
            ]);

            foreach ($request->sampah_id as $index => $sampahId) {
                $berat = $request->berat[$index];
                $jenisSampah = JenisSampah::where('id', $sampahId)->lockForUpdate()->firstOrFail();

                $subtotal = $jenisSampah->harga_per_kg * $berat;
                $totalHarga += $subtotal;

                $setoran->details()->create([
                    'sampah_id' => $jenisSampah->id,
                    'berat' => $berat,
                    'harga_per_kg' => $jenisSampah->harga_per_kg,
                    'subtotal' => $subtotal,
                ]);

                $jenisSampah->increment('stok', $berat);
            }

            $setoran->update(['total_harga' => $totalHarga]);

            $setoran->nasabah->dompet->increment('saldo_rupiah', $totalHarga);

            DB::commit();

            // Return JSON sukses untuk AJAX — redirect dilakukan oleh frontend
            return response()->json([
                'success' => true,
                'redirect' => route('admin.setoran.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Return JSON error agar modal/error ditampilkan tanpa reload halaman
            return response()->json([
                'error' => 'Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function void(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $setoran = Setoran::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($setoran->status !== 'berhasil') {
                DB::rollBack();
                return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
            }

            // Reversal: kurangi stok sampah (kembalikan ke sebelum setoran)
            foreach ($setoran->details as $detail) {
                $jenisSampah = JenisSampah::where('id', $detail->sampah_id)->lockForUpdate()->firstOrFail();

                if ($jenisSampah->stok < $detail->berat) {
                    throw new \Exception("Stok sampah {$jenisSampah->nama_jenis} tidak mencukupi untuk reversal.");
                }

                $jenisSampah->decrement('stok', $detail->berat);
            }

            // Reversal: kurangi saldo rupiah nasabah
            $dompet = DompetNasabah::where('nasabah_id', $setoran->nasabah_id)->lockForUpdate()->firstOrFail();

            if ($dompet->saldo_rupiah < $setoran->total_harga) {
                throw new \Exception('Saldo rupiah nasabah tidak mencukupi untuk reversal.');
            }

            $dompet->decrement('saldo_rupiah', $setoran->total_harga);

            $setoran->update([
                'status' => 'dibatalkan',
                'alasan_batal' => $request->alasan_batal,
            ]);

            DB::commit();
            return redirect()->route('admin.setoran.index')->with('success', 'Transaksi setoran berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
