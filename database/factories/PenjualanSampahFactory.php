<?php

namespace Database\Factories;

use App\Models\Pengepul;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PenjualanSampah>
 */
class PenjualanSampahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pengepul_id' => Pengepul::factory(),
            'tanggal'     => $this->faker->date(),
            'total_harga' => $this->faker->numberBetween(10000, 1000000),
        ];
    }
}
