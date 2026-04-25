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
        Schema::table('sampahs', function (Blueprint $table) {
            $table->decimal('stok', 10, 2)->default(0)->after('harga_per_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sampahs', function (Blueprint $table) {
            $table->dropColumn('stok');
        });
    }
};
