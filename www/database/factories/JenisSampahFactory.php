<?php

namespace Database\Factories;

use App\Models\KategoriSampah;
use Illuminate\Database\Eloquent\Factories\Factory;

class JenisSampahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kategori_id'  => KategoriSampah::factory(),
            'nama_jenis'   => $this->faker->word() . ' ' . $this->faker->randomElement(['botol', 'kardus', 'kaleng', 'koran']),
            'harga_per_kg' => $this->faker->numberBetween(500, 5000),
        ];
    }
}
