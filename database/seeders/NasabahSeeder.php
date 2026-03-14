<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nasabah; // Pastikan Model di-import

class NasabahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nasabahs = [
            [
                'nik' => '3201021204990011',
                'nama' => 'MOH VICRI ADITIYA',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1989-01-01',
                'alamat' => 'Perum Gunung Putri Permai',
                'no_hp' => '081315005075',
            ],
            [
                'nik' => '3201024506920001',
                'nama' => 'SITI AMINAH',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1992-06-15',
                'alamat' => 'Jl. Mawar No. 12, Jakarta Timur',
                'no_hp' => '081222333444',
            ],
            [
                'nik' => '3201022108850005',
                'nama' => 'BUDI SANTOSO',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-08-21',
                'alamat' => 'Gg. Bakti No. 5, Bandung',
                'no_hp' => '085677889900',
            ],
            [
                'nik' => '3201026011950002',
                'nama' => 'DEWI LESTARI',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1995-11-20',
                'alamat' => 'Perum Permata Hijau Blok C',
                'no_hp' => '081900112233',
            ],
            [
                'nik' => '3201020303880008',
                'nama' => 'AHMAD FAUZI',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1988-03-03',
                'alamat' => 'Jl. Melati No. 45, Malang',
                'no_hp' => '082133445566',
            ],
            [
                'nik' => '3201025212900003',
                'nama' => 'RINA WATI',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1990-12-12',
                'alamat' => 'Perum Candi Indah, Semarang',
                'no_hp' => '087755667788',
            ],
            [
                'nik' => '3201021507800004',
                'nama' => 'JOKO SUSILO',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1980-07-15',
                'alamat' => 'Jl. Malioboro No. 100',
                'no_hp' => '081122233344',
            ],
            [
                'nik' => '3201024805980007',
                'nama' => 'ANISA PUTRI',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1998-05-08',
                'alamat' => 'Jl. Slamet Riyadi No. 22',
                'no_hp' => '081544556677',
            ],
            [
                'nik' => '3201020909930009',
                'nama' => 'RIZKY RAMADHAN',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1993-09-09',
                'alamat' => 'Jl. Sudirman No. 1, Medan',
                'no_hp' => '081299887766',
            ],
            [
                'nik' => '3201024202870006',
                'nama' => 'LINDA KUSUMA',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '1987-02-02',
                'alamat' => 'Jl. Legian No. 15, Kuta',
                'no_hp' => '081388776655',
            ],
        ];

        foreach ($nasabahs as $nasabah) {
            Nasabah::create($nasabah);
        }
    }
}
