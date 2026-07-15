<?php

namespace Database\Seeders;

use App\Models\DompetNasabah;
use App\Models\JenisSampah;
use App\Models\Nasabah;
use App\Models\Setoran;
use App\Models\SetoranDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetoranSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID');
        $nasabahIds = Nasabah::pluck('id')->toArray();
        $jenisSampahs = JenisSampah::all();

        // Pre-build dompet map: nasabah_id => dompet_id (for increment without loading model)
        $dompetMap = DompetNasabah::pluck('id', 'nasabah_id')->toArray();

        for ($month = 1; $month <= 12; $month++) {
            $count = $faker->numberBetween(5, 20);

            for ($i = 0; $i < $count; $i++) {
                $day = $faker->numberBetween(1, 28);
                $date = Carbon::create(2026, $month, $day);

                // 85% berhasil, 15% dibatalkan
                $isDibatalkan = $faker->numberBetween(1, 100) <= 15;
                $status = $isDibatalkan ? 'dibatalkan' : 'berhasil';

                $nasabahId = $faker->randomElement($nasabahIds);

                $setoran = Setoran::create([
                    'nasabah_id' => $nasabahId,
                    'total_harga' => 0,
                    'status' => $status,
                    'alasan_batal' => $isDibatalkan ? $faker->sentence() : null,
                ]);

                // Override timestamps
                $setoran->timestamps = false;
                $setoran->created_at = $date;
                $setoran->updated_at = $date;
                $setoran->save();

                // Buat 1-3 detail items
                $detailCount = $faker->numberBetween(1, 3);
                $totalHarga = 0;

                for ($j = 0; $j < $detailCount; $j++) {
                    $berat = $faker->randomFloat(2, 1, 50);
                    $jenis = $jenisSampahs->random();
                    $harga = $jenis->harga_per_kg;
                    $subtotal = round($berat * $harga, 2);
                    $totalHarga += $subtotal;

                    $detail = SetoranDetail::create([
                        'setoran_id' => $setoran->id,
                        'sampah_id' => $jenis->id,
                        'berat' => $berat,
                        'harga_per_kg' => $harga,
                        'subtotal' => $subtotal,
                    ]);

                    $detail->timestamps = false;
                    $detail->created_at = $date;
                    $detail->updated_at = $date;
                    $detail->save();

                    // Hanya untuk status berhasil: tambahkan stok sampah
                    if ($status === 'berhasil') {
                        $jenis->increment('stok', $berat);
                    }
                }

                // Update total_harga
                $setoran->update(['total_harga' => $totalHarga]);

                // Hanya untuk status berhasil: tambahkan saldo dompet nasabah
                if ($status === 'berhasil' && isset($dompetMap[$nasabahId])) {
                    DB::table('dompet_nasabahs')
                        ->where('id', $dompetMap[$nasabahId])
                        ->increment('saldo_rupiah', $totalHarga);
                }
            }
        }
    }
}
