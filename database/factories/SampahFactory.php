<?php

namespace Database\Factories;

use App\Models\JenisSampah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sampah>
 */
class SampahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jenis_sampah_id' => JenisSampah::factory(),
            'nama_sampah'     => $this->faker->word() . ' ' . $this->faker->randomElement(['botol', 'kardus', 'kaleng', 'koran']),
            'harga_per_kg'    => $this->faker->numberBetween(500, 5000),
        ];
    }
}
