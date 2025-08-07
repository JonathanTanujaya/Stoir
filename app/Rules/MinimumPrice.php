<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\DBarang;

class MinimumPrice implements ValidationRule
{
    protected $kodebarang;

    public function __construct($kodebarang)
    {
        $this->kodebarang = $kodebarang;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $barang = DBarang::where('kodebarang', $this->kodebarang)->first();

        if (!$barang) {
            $fail('The selected item does not exist.');
            return;
        }

        $minPrice = $barang->harga_minimum ?? 0;

        if ($value < $minPrice) {
            $fail("Price is below minimum. Minimum price: {$minPrice}, Proposed: {$value}");
        }
    }
}
