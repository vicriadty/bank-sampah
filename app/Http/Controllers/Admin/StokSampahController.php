<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;


use App\Models\Sampah;
use Illuminate\Http\Request;

class StokSampahController extends Controller
{
    public function index()
    {
        $stokSampah = Sampah::with(['jenisSampah'])
            ->select('id', 'nama_sampah', 'jenis_sampah_id')
            ->withSum('setoranDetails', 'berat')
            ->withSum('penjualanSampahs', 'berat')
            ->get()
            ->map(function ($sampah) {
                $totalBerat = ($sampah->setoran_details_sum_berat ?? 0) - ($sampah->penjualan_sampahs_sum_berat ?? 0);
                return (object) [
                    'sampah' => $sampah,
                    'total_berat' => $totalBerat,
                ];
            });

        // dd($stokSampah);
        return view('admin.stok-sampah.index', [
            'stokSampah' => $stokSampah,
        ]);
    }
}
