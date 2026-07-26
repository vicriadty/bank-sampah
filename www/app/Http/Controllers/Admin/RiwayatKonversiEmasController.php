<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\RiwayatKonversiEmas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RiwayatKonversiEmasController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatKonversiEmas::with('nasabah')->latest();

        if ($request->filled('nasabah')) {
            $query->whereHas('nasabah', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nasabah . '%');
            });
        }

        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        if ($tanggalAwal && $tanggalAkhir) {
            $query->whereDate('created_at', '>=', $tanggalAwal)
                  ->whereDate('created_at', '<=', $tanggalAkhir);
        }

        if ($request->action == 'cetak') {
            $riwayat = $query->get();
            $pdf = Pdf::loadView('admin.transaksi.riwayat-konversi-emas.laporan_pdf', compact('riwayat', 'tanggalAwal', 'tanggalAkhir'))
                ->setPaper('A4', 'landscape');

            $tanggal = now()->format('d-m-y');
            $namaFile = 'laporan-konversi-emas-' . $tanggal . '.pdf';

            return $pdf->stream($namaFile);
        }

        $riwayat = $query->paginate(10)->appends($request->query());

        $masterSwitch = Pengaturan::getValue('master_switch_auto_convert', '0');

        $hasFilter = $request->filled('nasabah') || $request->filled('tanggal_awal');
        $hasData = $riwayat->total() > 0;

        return view('admin.transaksi.riwayat-konversi-emas.index', compact('riwayat', 'masterSwitch', 'hasFilter', 'hasData'));
    }

    public function toggleMasterSwitch()
    {
        $setting = Pengaturan::firstOrCreate(
            ['key' => 'master_switch_auto_convert'],
            ['value' => '1']
        );

        $setting->update([
            'value' => $setting->value === '1' ? '0' : '1',
        ]);

        $status = $setting->value === '1' ? 'ON' : 'OFF';
        return back()->with('success', "Master Switch auto-convert: {$status}");
    }
}
