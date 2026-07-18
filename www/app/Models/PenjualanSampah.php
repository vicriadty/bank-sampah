<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanSampah extends Model
{
    use HasFactory;

    protected $fillable = ['pengepul_id', 'tanggal', 'total_harga', 'keterangan', 'status', 'alasan_batal', 'kode_penjualan'];

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

    protected static function booted()
    {
        static::created(function ($penjualan) {
            if (!$penjualan->kode_penjualan) {
                $penjualan->update(['kode_penjualan' => 'P' . str_pad($penjualan->id, 4, '0', STR_PAD_LEFT)]);
            }
        });
    }
}
