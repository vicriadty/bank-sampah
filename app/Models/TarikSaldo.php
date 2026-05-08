<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarikSaldo extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'jumlah_tarik', 'status', 'keterangan'];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
