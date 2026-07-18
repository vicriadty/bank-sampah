<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatKonversiEmas extends Model
{
    protected $table = 'riwayat_konversi_emas';

    protected $fillable = [
        'nasabah_id',
        'saldo_terpakai',
        'harga_emas_per_gram',
        'jumlah_gram',
        'sisa_saldo_rupiah',
        'total_saldo_emas',
    ];

    protected $casts = [
        'saldo_terpakai'      => 'decimal:2',
        'harga_emas_per_gram' => 'decimal:2',
        'jumlah_gram'         => 'decimal:4',
        'sisa_saldo_rupiah'   => 'decimal:2',
        'total_saldo_emas'    => 'decimal:4',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }
}
