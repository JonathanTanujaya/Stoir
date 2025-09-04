<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokClaim extends Model
{
    use HasFactory;

    protected $table = 'stok_claim';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodebarang',
        'stokclaim',
        'modal',
    ];

    protected $casts = [
        'stokclaim' => 'decimal:4',
        'modal' => 'decimal:4',
    ];

    /**
     * Relationship: Barang (item)
     */
    public function barang()
    {
        return $this->belongsTo(DBarang::class, 'kodebarang', 'kodebarang');
    }

    /**
     * Scope: Filter by item code
     */
    public function scopeByBarang($query, $kodeBarang)
    {
        return $query->where('kodebarang', $kodeBarang);
    }

    /**
     * Scope: Items with claim stock > 0
     */
    public function scopeHasClaim($query)
    {
        return $query->where('stokclaim', '>', 0);
    }

    /**
     * Calculate total claim value
     */
    public function getTotalClaimValueAttribute()
    {
        return $this->stokclaim * $this->modal;
    }

    /**
     * Check if item has claim stock
     */
    public function hasClaimStock()
    {
        return $this->stokclaim > 0;
    }

    /**
     * Get claim percentage against total stock
     */
    public function getClaimPercentage()
    {
        if ($this->barang && $this->barang->stok > 0) {
            return ($this->stokclaim / $this->barang->stok) * 100;
        }
        return 0;
    }
}
