<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'nasabah_id',
        'jumlah_saldo',
        'harga_emas_per_gram',
        'jumlah_gram',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getFormattedSaldoAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_saldo, 0, ',', '.');
    }

    public function getFormattedGramAttribute(): string
    {
        return number_format($this->jumlah_gram, 4, ',', '.') . ' gram';
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_emas_per_gram, 0, ',', '.');
    }
}
