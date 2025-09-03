<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaanDetail extends Model
{
    use HasFactory;

    protected $table = 'part_penerimaan_detail';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_divisi',
        'no_penerimaan',
        'kode_barang',
        'qty_supply',
        'harga',
        'diskon1',
        'diskon2',
        'harga_nett'
    ];

    protected $casts = [
        'qty_supply' => 'integer',
        'harga' => 'decimal:2',
        'diskon1' => 'decimal:2',
        'diskon2' => 'decimal:2',
        'harga_nett' => 'decimal:2'
    ];

    // Relationships (simplified)
    public function partPenerimaan()
    {
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan');
    }

    public function barang()
    {
        return $this->belongsTo(MasterBarang::class, 'kode_barang', 'kode_barang');
    }

    // Helper methods
    public function getSubtotal(): float
    {
        return $this->qty_supply * $this->harga;
    }

    public function getTotalDiscount(): float
    {
        $subtotal = $this->getSubtotal();
        $afterDiskon1 = $subtotal - ($subtotal * $this->diskon1 / 100);
        return ($subtotal * $this->diskon1 / 100) + ($afterDiskon1 * $this->diskon2 / 100);
    }

    public function getNettoAmount(): float
    {
        return $this->getSubtotal() - $this->getTotalDiscount();
    }

    public function getDiscount1Amount(): float
    {
        return $this->getSubtotal() * $this->diskon1 / 100;
    }

    public function getDiscount2Amount(): float
    {
        $afterDiskon1 = $this->getSubtotal() - $this->getDiscount1Amount();
        return $afterDiskon1 * $this->diskon2 / 100;
    }
}
