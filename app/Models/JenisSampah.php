<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    use HasFactory;

    protected $fillable = ['kategori_id', 'nama_jenis', 'harga_per_kg', 'stok'];

    public function kategoriSampah()
    {
        return $this->belongsTo(KategoriSampah::class, 'kategori_id');
    }

    public function setoranDetails()
    {
        return $this->hasMany(SetoranDetail::class, 'sampah_id');
    }

    public function penjualanSampahs()
    {
        return $this->hasMany(DetailPenjualanSampah::class, 'sampah_id');
    }
}
