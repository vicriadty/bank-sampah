<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSampah;
use App\Models\Sampah;

class JenisSampahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Kertas' => [
                ['nama_sampah' => 'Kertas Buram', 'harga_per_kg' => 1500],
                ['nama_sampah' => 'Koran', 'harga_per_kg' => 1000],
                ['nama_sampah' => 'Kardus', 'harga_per_kg' => 1200],
            ],
            'Plastik' => [
                ['nama_sampah' => 'Botol Plastik', 'harga_per_kg' => 2500],
                ['nama_sampah' => 'Gelas Plastik', 'harga_per_kg' => 2200],
                ['nama_sampah' => 'Plastik Daur Ulang', 'harga_per_kg' => 2000],
            ],
            'Logam' => [
                ['nama_sampah' => 'Aluminium', 'harga_per_kg' => 8000],
                ['nama_sampah' => 'Tembaga', 'harga_per_kg' => 15000],
                ['nama_sampah' => 'Besi', 'harga_per_kg' => 5000],
            ],
        ];

        foreach ($data as $jenis => $sampahs) {
            $jenisSampah = JenisSampah::create(['nama_jenis' => $jenis]);

            foreach ($sampahs as $sampah) {
                Sampah::create([
                    'jenis_sampah_id' => $jenisSampah->id,
                    'nama_sampah' => $sampah['nama_sampah'],
                    'harga_per_kg' => $sampah['harga_per_kg'],
                ]);
            }
        }
    }
}
