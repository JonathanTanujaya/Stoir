<?php

namespace App\Traits;

trait HasCompositeKey
{
    /**
     * Get the value of the model's route key.
     */
    public function getRouteKey()
    {
        return implode('-', [
            $this->getAttribute('kode_divisi'),
            $this->getAttribute('kode_barang')
        ]);
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (str_contains($value, '-')) {
            [$kodeDivisi, $kodeBarang] = explode('-', $value, 2);
            return $this->where('kode_divisi', $kodeDivisi)
                       ->where('kode_barang', $kodeBarang)
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
        return 'composite_key';
    }
}
