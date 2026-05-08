<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tarik_saldos', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('jumlah_tarik');
            $table->text('keterangan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tarik_saldos', function (Blueprint $table) {
            $table->dropColumn(['status', 'keterangan']);
        });
    }
};
