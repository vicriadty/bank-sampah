<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_penjualan_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_sampah_id')->constrained('penjualan_sampahs')->onDelete('cascade');
            $table->foreignId('sampah_id')->constrained('sampahs')->onDelete('cascade');
            $table->decimal('berat', 10, 2);
            $table->decimal('harga_per_kg', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan_sampahs');
    }
};
