<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSalesDetail extends Model
{
    protected $table = 'return_sales_detail';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'no_retur',
        'no_invoice',
        'kode_barang',
        'qty_retur',
        'harga_nett',
        'status'
    ];

    protected $casts = [
        'qty_retur' => 'integer',
        'harga_nett' => 'decimal:2'
    ];

    // Simplified relationships after removing kode_divisi composite key constraints
    
    public function returnSales(): BelongsTo
    {
        return $this->belongsTo(ReturnSales::class, 'no_retur', 'no_retur');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice');
    }
}
