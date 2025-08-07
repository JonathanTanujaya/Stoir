<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\DBarang;

class StockAvailable implements ValidationRule
{
    protected $kodebarang;
    protected $kodedivisi;

    public function __construct($kodebarang, $kodedivisi = null)
    {
        $this->kodebarang = $kodebarang;
        $this->kodedivisi = $kodedivisi;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $barang = DBarang::where('kodebarang', $this->kodebarang);
        
        if ($this->kodedivisi) {
            $barang->where('kodedivisi', $this->kodedivisi);
        }
        
        $barang = $barang->first();

        if (!$barang) {
            $fail('The selected item does not exist.');
            return;
        }

        $currentStock = $barang->stok ?? 0;
        
        if ($value > $currentStock) {
            $fail("Insufficient stock. Available: {$currentStock}, Requested: {$value}");
        }
    }
}
