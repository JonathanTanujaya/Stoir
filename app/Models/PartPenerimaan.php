<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartPenerimaan extends Model
{
    protected $table = 'part_penerimaan';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_penerimaan'
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

    public function partPenerimaanDetails(): HasMany
    {
        return $this->hasMany(PartPenerimaanDetail::class, ['kode_divisi', 'no_penerimaan'], ['kode_divisi', 'no_penerimaan']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_penerimaan'];
    }
}
