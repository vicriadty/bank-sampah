<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sampah extends Model
{
    protected $fillable = ['jenis_sampah_id', 'nama_sampah', 'harga_per_kg'];

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class);
    }


    public function setoranDetails()
    {
        // return $this->hasMany(SetoranDetail::class);
    }
}
