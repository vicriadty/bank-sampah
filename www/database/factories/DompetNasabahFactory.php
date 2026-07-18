<?php

namespace Database\Factories;

use App\Models\DompetNasabah;
use App\Models\Nasabah;
use Illuminate\Database\Eloquent\Factories\Factory;

class DompetNasabahFactory extends Factory
{
    protected $model = DompetNasabah::class;

    public function definition(): array
    {
        return [
            'nasabah_id'      => Nasabah::factory(),
            'saldo_rupiah'    => 0,
            'saldo_emas_gram' => 0,
        ];
    }

    public function withSaldo(int $saldo): static
    {
        return $this->state(fn (array $attributes) => [
            'saldo_rupiah' => $saldo,
        ]);
    }
}
