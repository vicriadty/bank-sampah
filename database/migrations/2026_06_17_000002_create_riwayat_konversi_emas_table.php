<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_konversi_emas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained()->onDelete('cascade');
            $table->decimal('saldo_terpakai', 12, 2);
            $table->decimal('harga_emas_per_gram', 14, 2);
            $table->decimal('jumlah_gram', 10, 4);
            $table->decimal('sisa_saldo_rupiah', 12, 2);
            $table->decimal('total_saldo_emas', 10, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_konversi_emas');
    }
};
