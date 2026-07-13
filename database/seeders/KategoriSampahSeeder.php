<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriSampah;
use App\Models\JenisSampah;

class KategoriSampahSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Kertas' => [
                ['nama_jenis' => 'Kertas Buram', 'harga_per_kg' => 1500],
                ['nama_jenis' => 'Koran', 'harga_per_kg' => 1000],
                ['nama_jenis' => 'Kardus', 'harga_per_kg' => 1200],
            ],
            'Plastik' => [
                ['nama_jenis' => 'Botol Plastik', 'harga_per_kg' => 2500],
                ['nama_jenis' => 'Gelas Plastik', 'harga_per_kg' => 2200],
                ['nama_jenis' => 'Plastik Daur Ulang', 'harga_per_kg' => 2000],
            ],
            'Logam' => [
                ['nama_jenis' => 'Aluminium', 'harga_per_kg' => 8000],
                ['nama_jenis' => 'Tembaga', 'harga_per_kg' => 15000],
                ['nama_jenis' => 'Besi', 'harga_per_kg' => 5000],
            ],
        ];

        foreach ($data as $kategori => $jenisList) {
            $kategoriSampah = KategoriSampah::create(['nama_kategori' => $kategori]);

            foreach ($jenisList as $jenis) {
                JenisSampah::create([
                    'kategori_id' => $kategoriSampah->id,
                    'nama_jenis' => $jenis['nama_jenis'],
                    'harga_per_kg' => $jenis['harga_per_kg'],
                ]);
            }
        }
    }
}
