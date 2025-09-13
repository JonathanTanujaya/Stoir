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
        'kode_divisi',
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

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'kode_supplier', 'kode_supplier')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function partPenerimaan(): BelongsTo
    {
        return $this->belongsTo(PartPenerimaan::class, 'no_penerimaan', 'no_penerimaan')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function returPenerimaanDetails(): HasMany
    {
        return $this->hasMany(ReturPenerimaanDetail::class, ['kode_divisi', 'no_retur_penerimaan'], ['kode_divisi', 'no_retur_penerimaan']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_retur_penerimaan'];
    }
}
