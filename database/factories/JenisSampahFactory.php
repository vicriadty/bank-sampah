<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JenisSampah>
 */
class JenisSampahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_jenis' => $this->faker->randomElement(['Plastik', 'Kertas', 'Logam', 'Kaca', 'Organik']),
        ];
    }
}
