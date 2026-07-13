<?php

namespace Database\Seeders;

use App\Models\Pengepul;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengepulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pengepuls = [
            [
                'nama' => 'UD. Jaya Abadi',
                'alamat' => 'Jl. Merdeka No. 1, Jakarta',
                'no_hp' => '081234567890',
                'status' => 'Aktif',
                'keterangan' => 'Menerima kardus dan plastik',
            ],
            [
                'nama' => 'Pengepul Barokah',
                'alamat' => 'Jl. Melati No. 12, Bogor',
                'no_hp' => '081345678901',
                'status' => 'Aktif',
                'keterangan' => 'Spesialis logam dan besi tua',
            ],
            [
                'nama' => 'Lestari Plastik',
                'alamat' => 'Perum Indah Permai, Bekasi',
                'no_hp' => '081456789012',
                'status' => 'Aktif',
                'keterangan' => 'Hanya menerima botol plastik PET',
            ],
            [
                'nama' => 'Sinar Logam',
                'alamat' => 'Jl. Industri Raya No. 5, Depok',
                'no_hp' => '081567890123',
                'status' => 'Tidak Aktif',
                'keterangan' => 'Sedang renovasi gudang',
            ],
            [
                'nama' => 'Berkah Rejeki',
                'alamat' => 'Jl. Pahlawan No. 45, Tangerang',
                'no_hp' => '081678901234',
                'status' => 'Aktif',
                'keterangan' => 'Siap jemput ke lokasi nasabah',
            ],
            [
                'nama' => 'Hijau Berseri',
                'alamat' => 'Gg. Swadaya No. 8, Bandung',
                'no_hp' => '081789012345',
                'status' => 'Aktif',
                'keterangan' => 'Khusus pengepul minyak jelantah',
            ],
            [
                'nama' => 'Toko Rombeng Ali',
                'alamat' => 'Jl. Veteran No. 22, Surabaya',
                'no_hp' => '081890123456',
                'status' => 'Aktif',
                'keterangan' => 'Buka setiap hari 08.00 - 17.00',
            ],
            [
                'nama' => 'Daur Ulang Mandiri',
                'alamat' => 'Kawasan Pergudangan B, Semarang',
                'no_hp' => '081901234567',
                'status' => 'Aktif',
                'keterangan' => 'Minimal setoran 50kg',
            ],
            [
                'nama' => 'Pengepul Pak Slamet',
                'alamat' => 'Jl. Mawar Putih, Yogyakarta',
                'no_hp' => '081211223344',
                'status' => 'Aktif',
                'keterangan' => 'Menerima kertas dan koran bekas',
            ],
            [
                'nama' => 'CV. Maju Terus',
                'alamat' => 'Jl. Gatot Subroto No. 99, Medan',
                'no_hp' => '081333445566',
                'status' => 'Tidak Aktif',
                'keterangan' => 'Tutup sementara',
            ],
        ];

        foreach ($pengepuls as $pengepul) {
            Pengepul::create($pengepul);
        }

        // 50 data faker untuk test pagination
        $faker = \Faker\Factory::create('id_ID');
        for ($i = 1; $i <= 50; $i++) {
            Pengepul::create([
                'nama' => $faker->company(),
                'alamat' => $faker->address(),
                'no_hp' => $faker->numerify('08##########'),
                'status' => $faker->randomElement(['Aktif', 'Tidak Aktif']),
                'keterangan' => $faker->sentence(6),
            ]);
        }
    }
}
