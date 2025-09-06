<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'total_harga'];


    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function details()
    {
        return $this->hasMany(SetoranDetail::class);
    }
}
