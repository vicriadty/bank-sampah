<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{
    protected $fillable = ['sampah_id', 'nama_jenis', 'jenis_sampah_id', 'nama_sampah', 'harga_per_kg'];

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
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
