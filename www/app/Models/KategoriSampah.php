<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSampah extends Model
{
    use HasFactory;

    protected $fillable = ['nama_kategori', 'keterangan'];

    public function jenisSampahs()
    {
        return $this->hasMany(JenisSampah::class, 'kategori_id');
    }
}
