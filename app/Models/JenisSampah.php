<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSampah extends Model
{
    use HasFactory;
    protected $table = 'jenis_sampahs';
    protected $fillable = ['nama_jenis'];

    public function sampahs()
    {
        return $this->hasMany(Sampah::class);
    }
}
