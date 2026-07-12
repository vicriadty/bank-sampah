<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'total_harga', 'status', 'alasan_batal', 'kode_setoran'];

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

    protected static function booted()
    {
        static::created(function ($setoran) {
            if (!$setoran->kode_setoran) {
                $setoran->update(['kode_setoran' => 'S' . str_pad($setoran->id, 4, '0', STR_PAD_LEFT)]);
            }
        });
    }
}
