<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
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
            'kode_area' => strtoupper($this->faker->unique()->lexify('AR???')),
            'area' => $this->faker->city(),
            'status' => $this->faker->boolean(90), // 90% chance true
        ];
    }
}
