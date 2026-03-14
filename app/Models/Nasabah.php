<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';

    protected $fillable = ['nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'alamat', 'no_hp'];

    protected $guarded = [];

    public function setorans()
    {
        return $this->hasMany(Setoran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
