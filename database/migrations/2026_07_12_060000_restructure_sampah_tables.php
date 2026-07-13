<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setoran_details', function (Blueprint $table) {
            $table->dropForeign(['sampah_id']);
        });

        Schema::table('detail_penjualan_sampahs', function (Blueprint $table) {
            $table->dropForeign(['sampah_id']);
        });

        Schema::dropIfExists('sampahs');
        Schema::dropIfExists('jenis_sampahs');

        Schema::create('kategori_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('jenis_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_sampahs')->onDelete('cascade');
            $table->string('nama_jenis');
            $table->decimal('harga_per_kg', 10, 2);
            $table->decimal('stok', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('setoran_details', function (Blueprint $table) {
            $table->foreign('sampah_id')->references('id')->on('jenis_sampahs')->onDelete('cascade');
        });

        Schema::table('detail_penjualan_sampahs', function (Blueprint $table) {
            $table->foreign('sampah_id')->references('id')->on('jenis_sampahs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('setoran_details', function (Blueprint $table) {
            $table->dropForeign(['sampah_id']);
        });

        Schema::table('detail_penjualan_sampahs', function (Blueprint $table) {
            $table->dropForeign(['sampah_id']);
        });

        Schema::dropIfExists('jenis_sampahs');
        Schema::dropIfExists('kategori_sampahs');

        Schema::create('jenis_sampahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jenis');
            $table->timestamps();
        });

        Schema::create('sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_sampah_id')->constrained()->onDelete('cascade');
            $table->string('nama_sampah');
            $table->decimal('harga_per_kg', 10, 2);
            $table->decimal('stok', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('setoran_details', function (Blueprint $table) {
            $table->foreign('sampah_id')->references('id')->on('sampahs')->onDelete('cascade');
        });

        Schema::table('detail_penjualan_sampahs', function (Blueprint $table) {
            $table->foreign('sampah_id')->references('id')->on('sampahs')->onDelete('cascade');
        });
    }
};
