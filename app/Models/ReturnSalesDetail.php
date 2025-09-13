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
        'kode_divisi',
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

    // Note: These relationships are simplified due to composite key constraints
    // They may require additional where clauses in queries for proper scoping
    
    public function returnSales(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(ReturnSales::class, 'no_retur', 'no_retur');
    }

    public function barang(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    public function invoice(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice');
    }
}
