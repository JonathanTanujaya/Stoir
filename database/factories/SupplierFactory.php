<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'kode_supplier' => 'SUP' . $this->faker->unique()->numberBetween(100, 999),
            'nama_supplier' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'telp' => $this->faker->phoneNumber(),
            'contact' => $this->faker->name(),
            'status' => $this->faker->boolean(80),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => null,
            'telp' => null,
            'contact' => null,
        ]);
    }

    public function withFullContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', ' . $this->faker->secondaryAddress(),
            'telp' => $this->faker->phoneNumber(),
            'contact' => $this->faker->name() . ' (' . $this->faker->jobTitle() . ')',
        ]);
    }

    public function withCode(string $kodeSupplier): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_supplier' => strtoupper($kodeSupplier),
        ]);
    }

    public function jakarta(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', Jakarta',
        ]);
    }

    public function surabaya(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', Surabaya',
        ]);
    }
}