<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Nasabah>
 */
class NasabahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'nik'           => $this->faker->numerify('################'), // 16 digit
            'nama'          => $this->faker->name(),
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'tanggal_lahir' => $this->faker->date('Y-m-d', '-17 years'),
            'tempat_lahir'  => $this->faker->city(),
            'alamat'        => $this->faker->address(),
            'no_hp'         => $this->faker->numerify('08##########'),
            'saldo'         => 0,
        ];
    }

    /**
     * State dengan saldo tertentu.
     */
    public function withSaldo(int $saldo): static
    {
        return $this->state(fn (array $attributes) => [
            'saldo' => $saldo,
        ]);
    }
}
