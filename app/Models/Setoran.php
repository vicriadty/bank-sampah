<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'total_harga', 'status', 'alasan_batal'];

    protected $casts = [
        'status' => 'string',
    ];


    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function details()
    {
        return $this->hasMany(SetoranDetail::class);
    }
}
