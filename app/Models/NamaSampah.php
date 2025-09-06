<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NamaSampah extends Model
{
    protected $table = 'nama_sampah'; // sesuaikan jika berbeda
    protected $fillable = ['jenis_sampah_id', 'nama_sampah', 'harga'];

    public function jenisSampah()
    {
        return $this->belongsTo(JenisSampah::class, 'jenis_sampah_id');
    }
}
