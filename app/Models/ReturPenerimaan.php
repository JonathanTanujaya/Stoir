<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturPenerimaan extends Model
{
    protected $table = 'retur_penerimaan';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'no_retur_penerimaan',
        'tgl_retur',
        'kode_supplier',
        'no_penerimaan',
        'nilai',
        'keterangan'
    ];

    protected $casts = [
        'tgl_retur' => 'date',
        'nilai' => 'decimal:2'
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'kode_supplier', 'kode_supplier');
    }

    public function partPenerimaan(): BelongsTo
    {
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan');
    }

    public function returPenerimaanDetails(): HasMany
    {
        return $this->hasMany(ReturPenerimaanDetail::class, 'no_retur_penerimaan', 'no_retur_penerimaan');
    }

    public function getKeyName(): array
    {
        return ['no_retur_penerimaan'];
    }
}
