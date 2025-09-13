<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_divisi' => function () {
                // Create or get existing division
                return \App\Models\Divisi::firstOrCreate(
                    ['kode_divisi' => 'TEST'],
                    [
                        'nama_divisi' => 'Test Division'
                    ]
                )->kode_divisi;
            },
            'kode_supplier' => 'SUP' . $this->faker->unique()->numberBetween(100, 999),
            'nama_supplier' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'telp' => $this->faker->phoneNumber(),
            'contact' => $this->faker->name(),
            'status' => $this->faker->boolean(80), // 80% chance true
        ];
    }

    /**
     * Indicate that the supplier is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the supplier is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Create a supplier with minimal data (no optional fields).
     */
    public function minimal(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => null,
            'telp' => null,
            'contact' => null,
        ]);
    }

    /**
     * Create a supplier with complete contact information.
     */
    public function withFullContact(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', ' . $this->faker->secondaryAddress(),
            'telp' => $this->faker->phoneNumber(),
            'contact' => $this->faker->name() . ' (' . $this->faker->jobTitle() . ')',
        ]);
    }

    /**
     * Create a supplier for a specific division.
     */
    public function forDivision(string $kodeDivisi): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_divisi' => $kodeDivisi,
        ]);
    }

    /**
     * Create a supplier with a specific code.
     */
    public function withCode(string $kodeSupplier): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_supplier' => strtoupper($kodeSupplier),
        ]);
    }

    /**
     * Create a supplier in Jakarta.
     */
    public function jakarta(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', Jakarta',
        ]);
    }

    /**
     * Create a supplier in Surabaya.
     */
    public function surabaya(): static
    {
        return $this->state(fn (array $attributes) => [
            'alamat' => $this->faker->streetAddress() . ', Surabaya',
        ]);
    }
}
