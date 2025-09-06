<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengepul extends Model
{
    protected $fillable = ['nama', 'alamat', 'no_hp', 'status', 'keterangan'];


    public function penjualan()
    {
        return $this->hasMany(PenjualanSampah::class);
    }
}
