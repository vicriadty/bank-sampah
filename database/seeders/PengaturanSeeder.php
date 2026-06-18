<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::firstOrCreate(
            ['key' => 'master_switch_auto_convert'],
            ['value' => '1']
        );
    }
}
