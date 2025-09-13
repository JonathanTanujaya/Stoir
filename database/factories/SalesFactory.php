<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_divisi' => 'TEST',
            'kode_sales' => strtoupper($this->faker->unique()->lexify('SL???')),
            'nama_sales' => $this->faker->name(),
            'kode_area' => 'ARTEST',
            'alamat' => $this->faker->address(),
            'no_hp' => $this->faker->phoneNumber(),
            'target' => $this->faker->numberBetween(1000000, 10000000),
            'status' => true
        ];
    }
}
