<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\RiwayatKonversiEmas;
use Illuminate\Http\Request;

class GoldExchangeController extends Controller
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

        $riwayat = $query->paginate(10)->appends($request->query());

        $masterSwitch = Pengaturan::getValue('master_switch_auto_convert', '0');

        return view('admin.transaksi.gold-exchange.index', compact('riwayat', 'masterSwitch'));
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
