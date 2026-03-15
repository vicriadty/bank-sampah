<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    protected $table = 'jenis_sampahs';
    protected $fillable = ['nama_jenis'];

    public function sampahs()
    {
        return $this->hasMany(Sampah::class);
    }
}
