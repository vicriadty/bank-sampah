<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetoranDetail extends Model
{
    use HasFactory;

    protected $fillable = ['setoran_id', 'sampah_id', 'berat', 'harga_per_kg', 'subtotal', 'stok'];

    public function setoran()
    {
        return $this->belongsTo(Setoran::class);
    }

    public function sampah()
    {
        return $this->belongsTo(Sampah::class);
    }
}
