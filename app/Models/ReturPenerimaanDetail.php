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

    // Simplified relationships after removing kode_divisi composite key constraints
    
    public function returPenerimaan(): BelongsTo
    {
        return $this->belongsTo(ReturPenerimaan::class, 'no_retur', 'no_retur_penerimaan');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    public function partPenerimaan(): BelongsTo
    {
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan');
    }
}
