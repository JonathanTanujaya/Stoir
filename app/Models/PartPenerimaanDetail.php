<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartPenerimaanDetail extends Model
{
    use HasFactory;
    
    protected $table = 'part_penerimaan_detail';
    
    // No primary key defined in database schema
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
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
        'harga_nett' => 'decimal:2',
    ];

    /**
     * Relationship with PartPenerimaan
     */
    public function partPenerimaan(): BelongsTo
    {
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan');
    }

    /**
     * Relationship with Barang
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Override getKeyName to return null since no primary key is defined
     */
    public function getKeyName()
    {
        return null;
    }
}
