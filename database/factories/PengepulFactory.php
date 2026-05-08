<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pengepul>
 */
class PengepulFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama'    => $this->faker->company(),
            'alamat'  => $this->faker->address(),
            'no_hp'   => $this->faker->numerify('08##########'),
        ];
    }
}
