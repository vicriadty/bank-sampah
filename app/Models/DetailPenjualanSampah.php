<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualanSampah extends Model
{
    protected $fillable = ['penjualan_sampah_id', 'sampah_id', 'berat', 'harga_per_kg', 'subtotal', 'stok'];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanSampah::class, 'penjualan_sampah_id');
    }

    public function sampah()
    {
        return $this->belongsTo(Sampah::class);
    }
}
