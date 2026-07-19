<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->enum('status', ['berhasil', 'dibatalkan'])->default('berhasil')->after('total_harga');
            $table->text('alasan_batal')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            $table->dropColumn(['status', 'alasan_batal']);
        });
    }
};
