<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NasabahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengikuti logic NasabahController@store: buat User dulu, lalu Nasabah dengan user_id.
     */
    public function run(): void
    {
        $nasabahs = [
            [
                'nik' => '3201021204990018',
                'nama' => 'RAFFA PRADIPTA',
                'username' => 'raffa01',
                'password' => '12345678',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bogor',
                'tanggal_lahir' => '1989-01-01',
                'alamat' => 'Perum Gunung Putri Permai',
                'no_hp' => '081315005075',
                'saldo' => 0,
            ],
            [
                'nik' => '3201024506920001',
                'nama' => 'SITI AMINAH',
                'username' => 'siti01',
                'password' => '12345678',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1992-06-15',
                'alamat' => 'Jl. Mawar No. 12, Jakarta Timur',
                'no_hp' => '081222333444',
                'saldo' => 0,
            ],
            [
                'nik' => '3201022108850005',
                'nama' => 'BUDI SANTOSO',
                'username' => 'budi01',
                'password' => '12345678',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-08-21',
                'alamat' => 'Gg. Bakti No. 5, Bandung',
                'no_hp' => '085677889900',
                'saldo' => 0,
            ],
            [
                'nik' => '3201026011950002',
                'nama' => 'DEWI LESTARI',
                'username' => 'dewi01',
                'password' => '12345678',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1995-11-20',
                'alamat' => 'Perum Permata Hijau Blok C',
                'no_hp' => '081900112233',
                'saldo' => 0,
            ],
            [
                'nik' => '3201020303880008',
                'nama' => 'AHMAD FAUZI',
                'username' => 'ahmad01',
                'password' => '12345678',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1988-03-03',
                'alamat' => 'Jl. Melati No. 45, Malang',
                'no_hp' => '082133445566',
                'saldo' => 0,
            ],
            [
                'nik' => '3201025212900003',
                'nama' => 'RINA WATI',
                'username' => 'rina01',
                'password' => '12345678',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1990-12-12',
                'alamat' => 'Perum Candi Indah, Semarang',
                'no_hp' => '087755667788',
                'saldo' => 0,
            ],
            [
                'nik' => '3201021507800004',
                'nama' => 'JOKO SUSILO',
                'username' => 'joko01',
                'password' => '12345678',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1980-07-15',
                'alamat' => 'Jl. Malioboro No. 100',
                'no_hp' => '081122233344',
                'saldo' => 0,
            ],
            [
                'nik' => '3201024805980007',
                'nama' => 'ANISA PUTRI',
                'username' => 'anisa01',
                'password' => '12345678',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1998-05-08',
                'alamat' => 'Jl. Slamet Riyadi No. 22',
                'no_hp' => '081544556677',
                'saldo' => 0,
            ],
            [
                'nik' => '3201020909930009',
                'nama' => 'RIZKY RAMADHAN',
                'username' => 'rizky01',
                'password' => '12345678',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1993-09-09',
                'alamat' => 'Jl. Sudirman No. 1, Medan',
                'no_hp' => '081299887766',
                'saldo' => 0,
            ],
            [
                'nik' => '3201024202870006',
                'nama' => 'LINDA KUSUMA',
                'username' => 'linda01',
                'password' => '12345678',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Denpasar',
                'tanggal_lahir' => '1987-02-02',
                'alamat' => 'Jl. Legian No. 15, Kuta',
                'no_hp' => '081388776655',
                'saldo' => 0,
            ],
        ];

        foreach ($nasabahs as $data) {
            DB::transaction(function () use ($data) {
                $user = User::create([
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'role' => 'nasabah',
                ]);

                Nasabah::create([
                    'user_id' => $user->id,
                    'nik' => $data['nik'],
                    'nama' => $data['nama'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'alamat' => $data['alamat'],
                    'no_hp' => $data['no_hp'],
                ]);
            });
        }

        // Data faker per bulan (Jan-Des 2026) dengan created_at natural
        $faker = \Faker\Factory::create('id_ID');

        for ($month = 1; $month <= 12; $month++) {
            $count = $faker->numberBetween(5, 20);

            for ($i = 0; $i < $count; $i++) {
                $day = $faker->numberBetween(1, 28);
                $date = \Carbon\Carbon::create(2026, $month, $day);
                $uniqueId = $month . '_' . $i;

                DB::transaction(function () use ($faker, $date, $uniqueId) {
                    $nama = $faker->name();
                    $username = strtolower(str_replace(' ', '.', $nama)) . $uniqueId;
                    $username = substr($username, 0, 30);

                    $user = User::create([
                        'username' => $username,
                        'password' => Hash::make('12345678'),
                        'role' => 'nasabah',
                    ]);

                    $user->timestamps = false;
                    $user->created_at = $date;
                    $user->updated_at = $date;
                    $user->save();

                    $nasabah = Nasabah::create([
                        'user_id' => $user->id,
                        'nik' => $faker->numerify('################'),
                        'nama' => $nama,
                        'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                        'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                        'tempat_lahir' => $faker->city(),
                        'alamat' => $faker->address(),
                        'no_hp' => $faker->numerify('08##########'),
                    ]);

                    $nasabah->timestamps = false;
                    $nasabah->created_at = $date;
                    $nasabah->updated_at = $date;
                    $nasabah->save();
                });
            }
        }
    }
}
