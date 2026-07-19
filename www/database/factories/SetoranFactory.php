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
            'total_harga' => 0,
            'status' => 'berhasil',
            'alasan_batal' => null,
        ];
    }

    public function dibatalkan(string $alasan = null): static
    {
        return $this->state(fn () => [
            'status' => 'dibatalkan',
            'alasan_batal' => $alasan ?? fake()->sentence(),
        ]);
    }
}
