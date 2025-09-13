<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $companyTypes = ['PT', 'CV', 'UD', 'Toko', 'Perorangan'];
        $companyType = $this->faker->randomElement($companyTypes);
        $companyName = $companyType . ' ' . $this->faker->company();

        return [
            'kode_divisi' => 'TEST', // aligned with tests' setUp
            'kode_cust' => $this->generateKodeCustomer(),
            'nama_cust' => $companyName,
            'kode_area' => 'AR01',
            'alamat' => $this->faker->streetAddress(),
            'telp' => $this->faker->phoneNumber(),
            'contact' => strtoupper($this->faker->lexify('?????')),
            'credit_limit' => $this->faker->numberBetween(1_000_000, 1_000_000_000),
            'jatuh_tempo' => $this->faker->numberBetween(7, 60),
            'status' => $this->faker->boolean(85),
            'no_npwp' => $this->generateNpwp(),
            'nama_pajak' => $this->faker->company(),
            'alamat_pajak' => $this->faker->address(),
            'kode_sales' => 'SL01',
        ];
    }

    /**
     * Generate a customer code.
     */
    private function generateKodeCustomer(): string
    {
        $prefix = 'C';
        $number = $this->faker->unique()->numberBetween(1000, 9999);
        return $prefix . $number;
    }

    /**
     * Generate a valid NPWP format.
     */
    private function generateNpwp(): string
    {
        // Format: XX.XXX.XXX.X-XXX.XXX
        $part1 = $this->faker->numerify('##');
        $part2 = $this->faker->numerify('###');
        $part3 = $this->faker->numerify('###');
        $part4 = $this->faker->numerify('#');
        $part5 = $this->faker->numerify('###');
        $part6 = $this->faker->numerify('###');

        return "{$part1}.{$part2}.{$part3}.{$part4}-{$part5}.{$part6}";
    }

    /**
     * Create a customer with a specific division.
     */
    public function forDivision(string $kodeDivisi): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_divisi' => $kodeDivisi,
        ]);
    }

    /**
     * Create a customer with a specific area.
     */
    public function forArea(string $kodeArea): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_area' => $kodeArea,
        ]);
    }

    /**
     * Create a customer with a specific sales person.
     */
    public function forSales(string $kodeSales): static
    {
        return $this->state(fn (array $attributes) => [
            'kode_sales' => $kodeSales,
        ]);
    }

    /**
     * Create an active customer.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Create an inactive customer.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Create a customer with high credit limit.
     */
    public function highCredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $this->faker->numberBetween(500_000_000, 2_000_000_000),
        ]);
    }

    /**
     * Create a customer with low credit limit.
     */
    public function lowCredit(): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $this->faker->numberBetween(1_000_000, 50_000_000),
        ]);
    }

    /**
     * Create a customer with short payment terms.
     */
    public function shortPaymentTerms(): static
    {
        return $this->state(fn (array $attributes) => [
            'jatuh_tempo' => $this->faker->numberBetween(7, 14),
        ]);
    }

    /**
     * Create a customer with long payment terms.
     */
    public function longPaymentTerms(): static
    {
        return $this->state(fn (array $attributes) => [
            'jatuh_tempo' => $this->faker->numberBetween(45, 90),
        ]);
    }

    /**
     * Create a customer without NPWP.
     */
    public function withoutNPWP(): static
    {
        return $this->state(fn (array $attributes) => [
            'no_npwp' => null,
        ]);
    }

    /**
     * Create a corporate customer.
     */
    public function corporate(): static
    {
        return $this->state(function (array $attributes) {
            $corporateTypes = ['PT', 'CV'];
            $corporateType = $this->faker->randomElement($corporateTypes);
            $companyName = $corporateType . ' ' . $this->faker->company();

            return [
                'nama_cust' => $companyName,
                'credit_limit' => $this->faker->numberBetween(100_000_000, 1_000_000_000),
                'contact' => 'Direktur ' . $this->faker->name(),
            ];
        });
    }

    /**
     * Create a retail customer.
     */
    public function retail(): static
    {
        return $this->state(function (array $attributes) {
            $retailTypes = ['Toko', 'UD'];
            $retailType = $this->faker->randomElement($retailTypes);
            $storeName = $retailType . ' ' . $this->faker->firstName() . ' ' . $this->faker->lastName();

            return [
                'nama_cust' => $storeName,
                'credit_limit' => $this->faker->numberBetween(10_000_000, 100_000_000),
                'contact' => strtoupper($this->faker->lexify('?????')),
            ];
        });
    }

    /**
     * Create a customer with specific payment terms.
     */
    public function withPaymentTerms(int $days): static
    {
        return $this->state(fn (array $attributes) => [
            'jatuh_tempo' => $days,
        ]);
    }

    /**
     * Create a customer with specific credit limit.
     */
    public function withCreditLimit(int $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'credit_limit' => $amount,
        ]);
    }
}
