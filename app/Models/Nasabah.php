<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;

    protected $table = 'nasabahs';

    protected $fillable = ['user_id', 'nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'alamat', 'no_hp', 'email'];

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($nasabah) {
            $nasabah->dompet()->create([
                'saldo_rupiah'    => 0,
                'saldo_emas_gram' => 0,
            ]);
        });
    }

    public function dompet()
    {
        return $this->hasOne(DompetNasabah::class);
    }

    public function setorans()
    {
        return $this->hasMany(Setoran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function totalGoldGrams()
    {
        return $this->dompet->saldo_emas_gram ?? 0;
    }
}
