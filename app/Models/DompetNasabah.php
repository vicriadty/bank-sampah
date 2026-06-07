<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DompetNasabah extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'saldo_rupiah', 'saldo_emas_gram'];

    protected $casts = [
        'saldo_rupiah'    => 'decimal:2',
        'saldo_emas_gram' => 'decimal:4',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
