<?php

namespace Database\Factories;

use App\Models\Kategori;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    public function definition(): array
    {
        return [
            'kode_divisi' => Divisi::factory(),
            'kode_kategori' => $this->faker->unique()->lexify('CAT-???'),
            'kategori' => $this->faker->word(),
            'status' => true,
        ];
    }
}
