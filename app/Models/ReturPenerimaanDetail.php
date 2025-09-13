<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturPenerimaanDetail extends Model
{
    protected $table = 'retur_penerimaan_detail';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_retur',
        'no_penerimaan',
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
    
    public function returPenerimaan(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(ReturPenerimaan::class, 'no_retur', 'no_retur_penerimaan');
    }

    public function barang(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    public function partPenerimaan(): BelongsTo
    {
        // This is a simplified relationship - may need manual scoping by kode_divisi
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan');
    }
}
