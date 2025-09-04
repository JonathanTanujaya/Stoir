<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'company_name' => $this->faker->unique()->company(),
            'alamat' => $this->faker->address(),
            'kota' => $this->faker->city(),
            'an' => $this->faker->name(),
            'telp' => $this->faker->phoneNumber(),
            'npwp' => $this->faker->numerify('##.###.###.#-###.###'),
        ];
    }
}
