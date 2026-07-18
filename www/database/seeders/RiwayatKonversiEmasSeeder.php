<?php

namespace Database\Seeders;

use App\Models\Nasabah;
use App\Models\RiwayatKonversiEmas;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RiwayatKonversiEmasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $nasabahIds = Nasabah::pluck('id')->toArray();

        // Harga emas per gram range (2026: 1.600.000 - 1.850.000)
        $hargaAwal = 1_600_000;
        $hargaAkhir = 1_850_000;

        for ($month = 1; $month <= 12; $month++) {
            $count = $faker->numberBetween(3, 10);

            // Harga emas naik per bulan (tidak realistis tapi untuk visualisasi)
            $hargaPerBulan = $hargaAwal + (($hargaAkhir - $hargaAwal) / 12 * $month);

            for ($i = 0; $i < $count; $i++) {
                $day = $faker->numberBetween(1, 28);
                $date = Carbon::create(2026, $month, $day);

                $saldoTerpakai = $faker->randomFloat(2, 50_000, 500_000);
                $hargaGram = round($hargaPerBulan + $faker->randomFloat(0, -20_000, 20_000), 2);
                $jumlahGram = round($saldoTerpakai / $hargaGram, 4);
                $sisaSaldo = $faker->randomFloat(2, 10_000, 200_000);
                $totalSaldoEmas = $faker->randomFloat(4, 0.5, 15);

                $konversi = RiwayatKonversiEmas::create([
                    'nasabah_id' => $faker->randomElement($nasabahIds),
                    'saldo_terpakai' => $saldoTerpakai,
                    'harga_emas_per_gram' => $hargaGram,
                    'jumlah_gram' => $jumlahGram,
                    'sisa_saldo_rupiah' => $sisaSaldo,
                    'total_saldo_emas' => $totalSaldoEmas,
                ]);

                // Override timestamps
                $konversi->timestamps = false;
                $konversi->created_at = $date;
                $konversi->updated_at = $date;
                $konversi->save();
            }
        }
    }
}
