<?php

namespace Database\Factories;

use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setoran>
 */
class SetoranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nasabah_id' => Nasabah::factory(),
            'total_harga' => $this->faker->numberBetween(1000, 100000),
        ];
    }
}
