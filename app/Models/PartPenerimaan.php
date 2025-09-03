<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'part_penerimaan';
    protected $primaryKey = ['kode_divisi', 'no_penerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_divisi',
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
        'kurs' => 'decimal:4',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

    // Override methods for composite keys
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[$key] = $this->getAttribute($key);
        }
        return $keys;
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Relationships (simplified for composite key compatibility)
    public function supplier()
    {
        return $this->belongsTo(MasterSupplier::class, 'kode_supplier', 'kode_supplier');
    }

    public function divisi()
    {
        return $this->belongsTo(MasterDivisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function details()
    {
        return $this->hasMany(PartPenerimaanDetail::class, 'no_penerimaan', 'no_penerimaan');
    }

    // Scopes
    public function scopeBySupplier($query, $kodeDivisi, $kodeSupplier)
    {
        return $query->where('kode_divisi', $kodeDivisi)
                    ->where('kode_supplier', $kodeSupplier);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_penerimaan', [$startDate, $endDate]);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeFinish($query)
    {
        return $query->where('status', 'Finish');
    }

    public function scopeCancel($query)
    {
        return $query->where('status', 'Cancel');
    }

    // Helper methods
    public function isOpen(): bool
    {
        return $this->status === 'Open';
    }

    public function isFinish(): bool
    {
        return $this->status === 'Finish';
    }

    public function isCancel(): bool
    {
        return $this->status === 'Cancel';
    }

    public function getTotalWithTax(): float
    {
        return $this->total + $this->pajak - $this->discount;
    }

    public function getDiscountPercentage(): float
    {
        return $this->total > 0 ? ($this->discount / $this->total) * 100 : 0;
    }

    public function getTotalNetto(): float
    {
        return $this->total - $this->discount;
    }

    public function getTotalAfterTax(): float
    {
        return $this->getTotalNetto() + $this->pajak;
    }
}
