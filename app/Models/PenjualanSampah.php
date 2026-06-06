<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanSampah extends Model
{
    use HasFactory;

    protected $fillable = ['pengepul_id', 'tanggal', 'total_harga', 'keterangan', 'status', 'alasan_batal'];

    protected $casts = [
        'status' => 'string',
    ];

    public function pengepul()
    {
        return $this->belongsTo(Pengepul::class);
    }

    public function detail_penjualan()
    {
        return $this->hasMany(DetailPenjualanSampah::class);
    }

    //     public function detailPenjualanSampahs()
    //     {
    //         return $this->hasMany(DetailPenjualanSampah::class);
    //     }
}
