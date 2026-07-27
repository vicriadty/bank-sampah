<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\DetailPenjualanSampah;
use App\Models\KategoriSampah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\JenisSampah;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PenjualanSampahController extends Controller
{
    public function index(Request $request)
    {

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;
        $pengepulId = $request->pengepul_id;

        $query = PenjualanSampah::with('pengepul')->latest();

        if ($request->filled('pengepul')) {
            $search = $request->pengepul;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pengepul', function ($q2) use ($search) {
                    $q2->where('nama', 'like', '%' . $search . '%');
                })->orWhere('kode_penjualan', 'like', '%' . $search . '%');
            });
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereDate('created_at', '>=', $tanggalAwal)
                ->whereDate('created_at', '<=', $tanggalAkhir);
        }

        if ($pengepulId) {
            $query->where('pengepul_id', $pengepulId);
        }

        if ($request->action == 'cetak') {
            $penjualans = $query->get();
            $pdf = Pdf::loadView('admin.transaksi.penjualan-sampah.laporan_pdf', compact('penjualans', 'tanggalAwal', 'tanggalAkhir', 'pengepulId'))
                ->setPaper('A4', 'portrait');

            $tanggal = now()->format('d-m-y');

            $namaFile = 'laporan-penjualan-' . $tanggal . '.pdf';

            return $pdf->stream($namaFile);
        }

        // Default tampil data
        $penjualans = $query->paginate(10);
        $pengepuls = Pengepul::all();
        $kategoriSampah = KategoriSampah::all();
        $jenisSampahs = JenisSampah::with(['kategoriSampah'])
            ->select('id', 'nama_jenis', 'kategori_id', 'harga_per_kg', 'stok')
            ->get();

        $hasFilter = $request->filled('pengepul') || $request->filled('tanggal_awal');
        $hasData = $penjualans->total() > 0;

        return view('admin.transaksi.penjualan-sampah.index', compact('penjualans', 'pengepuls', 'kategoriSampah', 'jenisSampahs', 'hasFilter', 'hasData'));
    }

    public function create()
    {
        $pengepuls = Pengepul::all();
        $kategoriSampah = KategoriSampah::all();
        $jenisSampahs = JenisSampah::with(['kategoriSampah'])
            ->select('id', 'nama_jenis', 'kategori_id', 'harga_per_kg', 'stok')
            ->get();

        return view('admin.transaksi.penjualan-sampah.create', compact('pengepuls', 'kategoriSampah', 'jenisSampahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengepul_id' => ['required', 'exists:pengepuls,id'],
            'sampah_id' => ['required', 'array'],
            'sampah_id.*' => ['required', 'exists:jenis_sampahs,id'],
            'berat' => ['required', 'array'],
            'berat.*' => ['required', 'numeric', 'min:0.1'],
        ]);

        $errors = [];
        foreach ($request->sampah_id as $index => $sampahId) {
            $berat = $request->berat[$index] ?? null;

            if ($berat === null || $berat <= 0) {
                $errors["berat.{$index}"] = 'Berat sampah harus lebih dari 0.';
                continue;
            }

            $jenisSampah = JenisSampah::find($sampahId);
            if ($jenisSampah && $berat > $jenisSampah->stok) {
                $errors["berat.{$index}"] = 'Berat penjualan melebihi stok tersedia.';
            }
        }

        // Return JSON 422 untuk AJAX agar form tidak reset
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        DB::beginTransaction();

        try {
            $totalHarga = 0;

            $penjualan = PenjualanSampah::create([
                'pengepul_id' => $request->pengepul_id,
                'tanggal' => now(),
                'total_harga' => 0,
            ]);


            foreach ($request->sampah_id as $index => $sampahId) {
                $berat = $request->berat[$index];
                $jenisSampah = JenisSampah::where('id', $sampahId)->lockForUpdate()->firstOrFail();

                if ($berat > $jenisSampah->stok) {
                    throw new \Exception('Stok sampah ' . $jenisSampah->nama_jenis . ' tidak mencukupi.');
                }

                $subtotal = $berat * $jenisSampah->harga_per_kg;
                $totalHarga += $subtotal;

                DetailPenjualanSampah::create([
                    'penjualan_sampah_id' => $penjualan->id,
                    'sampah_id' => $sampahId,
                    'berat' => $berat,
                    'harga_per_kg' => $jenisSampah->harga_per_kg,
                    'subtotal' => $subtotal,
                ]);

                $jenisSampah->decrement('stok', $berat);
            }

            $penjualan->update(['total_harga' => $totalHarga]);

            DB::commit();

            // Return JSON sukses untuk AJAX — redirect dilakukan oleh frontend
            return response()->json([
                'success' => true,
                'redirect' => route('admin.penjualan.index'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Return JSON error agar modal/error ditampilkan tanpa reload halaman
            return response()->json([
                'error' => 'Gagal menyimpan penjualan: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function getSampahByJenis($id)
    {
        $jenisSampahs = JenisSampah::where('kategori_id', $id)->get();
        return response()->json($jenisSampahs);
    }


    public function void(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $penjualan = PenjualanSampah::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($penjualan->status !== 'berhasil') {
                DB::rollBack();
                return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
            }

            // Reversal: kembalikan stok sampah
            foreach ($penjualan->detail_penjualan as $detail) {
                $jenisSampah = JenisSampah::where('id', $detail->sampah_id)->lockForUpdate()->firstOrFail();
                $jenisSampah->increment('stok', $detail->berat);
            }

            $penjualan->update([
                'status' => 'dibatalkan',
                'alasan_batal' => $request->alasan_batal,
            ]);

            DB::commit();
            return redirect()->route('admin.penjualan.index')->with('success', 'Transaksi penjualan berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
