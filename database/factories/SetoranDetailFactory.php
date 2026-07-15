<?php

namespace Database\Factories;

use App\Models\JenisSampah;
use App\Models\Setoran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SetoranDetail>
 */
class SetoranDetailFactory extends Factory
{
    public function definition(): array
    {
        $berat = fake()->randomFloat(2, 1, 50);

        return [
            'setoran_id' => Setoran::factory(),
            'sampah_id' => JenisSampah::inRandomOrder()->first()->id,
            'berat' => $berat,
            'harga_per_kg' => 0,
            'subtotal' => 0,
        ];
    }

    public function withJenisSampah(JenisSampah $jenisSampah, float $berat = null): static
    {
        $berat = $berat ?? fake()->randomFloat(2, 1, 50);
        $harga = $jenisSampah->harga_per_kg;

        return $this->state(fn () => [
            'sampah_id' => $jenisSampah->id,
            'berat' => $berat,
            'harga_per_kg' => $harga,
            'subtotal' => round($berat * $harga, 2),
        ]);
    }
}
