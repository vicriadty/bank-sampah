<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\JenisSampah;
use Illuminate\Http\Request;

class StokSampahController extends Controller
{
    public function index()
    {
        $stokSampah = JenisSampah::with(['kategoriSampah'])
            ->select('id', 'nama_jenis', 'kategori_id')
            ->withSum('setoranDetails', 'berat')
            ->withSum('penjualanSampahs', 'berat')
            ->get()
            ->map(function ($jenisSampah) {
                $totalBerat = ($jenisSampah->setoran_details_sum_berat ?? 0) - ($jenisSampah->penjualan_sampahs_sum_berat ?? 0);
                $jenisSampah->update(['stok' => $totalBerat]);

                return (object) [
                    'sampah' => $jenisSampah,
                    'total_berat' => $totalBerat,
                ];
            });

        return view('admin.stok-sampah.index', [
            'stokSampah' => $stokSampah,
        ]);
    }
}
