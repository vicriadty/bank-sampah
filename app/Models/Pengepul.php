<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengepul extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'alamat', 'no_hp', 'status', 'keterangan'];


    public function penjualan()
    {
        return $this->hasMany(PenjualanSampah::class);
    }
}
