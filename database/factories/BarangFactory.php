<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Barang;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    protected $model = Barang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_divisi' => 'TEST',
            'kode_barang' => strtoupper($this->faker->unique()->bothify('BRG###')),
            'nama_barang' => $this->faker->words(3, true),
            'kode_kategori' => 'KAT001',
            'harga_list' => $this->faker->numberBetween(10000, 100000),
            'harga_jual' => $this->faker->numberBetween(12000, 120000),
            'satuan' => $this->faker->randomElement(['PCS', 'BOX', 'UNIT', 'SET']),
            'disc1' => $this->faker->numberBetween(0, 20),
            'disc2' => $this->faker->numberBetween(0, 10),
            'merk' => $this->faker->company(),
                        'barcode' => $this->faker->unique()->numerify('########'),
            'status' => $this->faker->boolean(80), // 80% chance true
            'lokasi' => $this->faker->randomElement(['A1', 'B2', 'C3', 'D4']),
            'stok_min' => $this->faker->numberBetween(1, 50)
        ];
    }

    /**
     * Indicate that the barang is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the barang is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Set specific divisi for the barang.
     */
    public function forDivisi(string $kodeDivisi): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_divisi' => $kodeDivisi,
        ]);
    }

    /**
     * Set specific category for the barang.
     */
    public function forKategori(string $kodeKategori): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_kategori' => $kodeKategori,
        ]);
    }
}
