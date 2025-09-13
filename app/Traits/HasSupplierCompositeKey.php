<?php

namespace App\Traits;

trait HasSupplierCompositeKey
{
    /**
     * Get the value of the model's route key.
     */
    public function getRouteKey()
    {
        return implode('-', [
            $this->getAttribute('kode_divisi'),
            $this->getAttribute('kode_supplier')
        ]);
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Get the divisi from route parameter
        $kodeDivisi = request()->route('kodeDivisi');
        
        if ($kodeDivisi && $field === null) {
            return $this->where('kode_divisi', $kodeDivisi)
                       ->where('kode_supplier', $value)
                       ->first();
        }

        // Handle composite key format
        if (str_contains($value, '-')) {
            [$kodeDivisi, $kodeSupplier] = explode('-', $value, 2);
            return $this->where('kode_divisi', $kodeDivisi)
                       ->where('kode_supplier', $kodeSupplier)
                       ->first();
        }

        // Fallback to normal resolution
        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'kode_supplier';
    }

    /**
     * Get the value of the model's primary key.
     */
    public function getKeyName()
    {
        return ['kode_divisi', 'kode_supplier'];
    }

    /**
     * Get the value of the model's primary key.
     */
    public function getKey()
    {
        return [
            'kode_divisi' => $this->getAttribute('kode_divisi'),
            'kode_supplier' => $this->getAttribute('kode_supplier')
        ];
    }
}