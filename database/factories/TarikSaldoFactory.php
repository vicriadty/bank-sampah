<?php

namespace Database\Factories;

use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TarikSaldo>
 */
class TarikSaldoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nasabah_id'  => Nasabah::factory(),
            'jumlah_tarik' => $this->faker->numberBetween(10000, 500000),
            'status'      => 'pending',
            'keterangan'  => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function rejected(?string $keterangan = null): static
    {
        return $this->state([
            'status'     => 'rejected',
            'keterangan' => $keterangan ?? 'Ditolak oleh admin',
        ]);
    }
}
