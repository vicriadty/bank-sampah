<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriSampahFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->randomElement(['Plastik', 'Kertas', 'Logam', 'Kaca', 'Organik']),
        ];
    }
}
