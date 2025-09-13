<?php

namespace Database\Factories;

use App\Models\Divisi;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisiFactory extends Factory
{
    protected $model = Divisi::class;

    public function definition(): array
    {
        return [
            'kode_divisi' => $this->faker->unique()->lexify('???') . $this->faker->unique()->numerify('##'),
            'nama_divisi' => $this->faker->company() . ' Division',
        ];
    }
}
