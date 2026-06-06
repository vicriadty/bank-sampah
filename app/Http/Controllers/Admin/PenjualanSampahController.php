<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\DetailPenjualanSampah;
use App\Models\Pengepul;
use App\Models\PenjualanSampah;
use App\Models\Sampah;
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
            $query->whereHas('pengepul', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->pengepul . '%');
            });
        }

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir]);
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

            return $pdf->download($namaFile);
        }

        // Default tampil data
        $penjualans = $query->paginate(10);
        $nasabahs = PenjualanSampah::all();
        return view('admin.transaksi.penjualan-sampah.index', compact('penjualans', 'pengepulId'));
    }

    public function create()
    {
        $pengepuls = Pengepul::all();
        $sampahs = Sampah::with(['jenisSampah'])
            ->select('id', 'nama_sampah', 'jenis_sampah_id', 'harga_per_kg', 'stok')
            ->get();

        return view('admin.transaksi.penjualan-sampah.create', compact('pengepuls', 'sampahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengepul_id' => ['required', 'exists:pengepuls,id'],
            'sampah_id' => ['required', 'array'],
            'sampah_id.*' => ['required', 'exists:sampahs,id'],
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

            $sampah = Sampah::find($sampahId);
            if ($sampah && $berat > $sampah->stok) {
                $errors["berat.{$index}"] = 'Berat penjualan melebihi stok tersedia.';
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
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
                $sampah = Sampah::where('id', $sampahId)->lockForUpdate()->firstOrFail();

                if ($berat > $sampah->stok) {
                    throw new \Exception('Stok sampah ' . $sampah->nama_sampah . ' tidak mencukupi.');
                }

                $subtotal = $berat * $sampah->harga_per_kg;
                $totalHarga += $subtotal;

                DetailPenjualanSampah::create([
                    'penjualan_sampah_id' => $penjualan->id,
                    'sampah_id' => $sampahId,
                    'berat' => $berat,
                    'harga_per_kg' => $sampah->harga_per_kg,
                    'subtotal' => $subtotal,
                ]);

                $sampah->decrement('stok', $berat);
            }

            $penjualan->update(['total_harga' => $totalHarga]);

            DB::commit();

            return redirect()->route('admin.penjualan.index')->with('success', 'Data Penjualan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan penjualan: ' . $e->getMessage()]);
        }
    }


    public function getSampahByJenis($id)
    {
        $sampahs = Sampah::where('jenis_sampah_id', $id)->get();
        return response()->json($sampahs);
    }


    // public function laporanPDF(Request $request)
    // {
    //     $request->validate([
    //         'tanggal' => 'required|date',
    //     ]);

    //     $tanggal = $request->tanggal;

    //     $penjualan_sampahs = PenjualanSampah::with(['pengepul', 'detail_penjualan.sampah'])
    //         ->whereDate('tanggal', $tanggal)
    //         ->get();

    //     if ($penjualan_sampahs->isEmpty()) {
    //         return back()->withErrors(['error' => 'Tidak ada data penjualan pada tanggal tersebut.']);
    //     }

    //     $pdf = Pdf::loadView('admin.transaksi.penjualan-sampah.laporan_pdf', [
    //         'penjualan_sampahs' => $penjualan_sampahs,
    //         'tanggal' => $tanggal,
    //     ]);

    //     return $pdf->download('laporan-penjualan-' . $tanggal . '.pdf');
    // }
}
