<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartPenerimaan extends Model
{
    protected $table = 'part_penerimaan';
    protected $primaryKey = 'no_penerimaan';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    
    protected $fillable = [
        'no_penerimaan',
        'tgl_penerimaan',
        'kode_valas',
        'kurs',
        'kode_supplier',
        'jatuh_tempo',
        'no_faktur',
        'total',
        'discount',
        'pajak',
        'grand_total',
        'status'
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'jatuh_tempo' => 'date',
        'kurs' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'kode_supplier', 'kode_supplier');
    }

    public function partPenerimaanDetails(): HasMany
    {
        return $this->hasMany(PartPenerimaanDetail::class, 'no_penerimaan', 'no_penerimaan');
    }

    public function returPenerimaanDetails(): HasMany
    {
        return $this->hasMany(ReturPenerimaanDetail::class, 'no_penerimaan', 'no_penerimaan');
    }

    public function dVouchers(): HasMany
    {
        return $this->hasMany(DVoucher::class, 'no_penerimaan', 'no_penerimaan');
    }
}
