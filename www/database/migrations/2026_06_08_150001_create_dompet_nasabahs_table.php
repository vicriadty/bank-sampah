<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dompet_nasabahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained()->onDelete('cascade');
            $table->decimal('saldo_rupiah', 12, 2)->default(0);
            $table->decimal('saldo_emas_gram', 10, 4)->default(0);
            $table->timestamps();
        });

        // Seed existing saldo dari tabel nasabahs ke dompet_nasabahs
        $nasabahs = DB::table('nasabahs')->get();
        foreach ($nasabahs as $nasabah) {
            DB::table('dompet_nasabahs')->insert([
                'nasabah_id'     => $nasabah->id,
                'saldo_rupiah'   => $nasabah->saldo ?? 0,
                'saldo_emas_gram' => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dompet_nasabahs');
    }
};
