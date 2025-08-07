<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'partpenerimaan';
    protected $primaryKey = ['kodedivisi', 'nopenerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'nopenerimaan',
        'tglpenerimaan',
        'kodevalas',
        'kurs',
        'kodesupplier',
        'jatuhtempo',
        'nofaktur',
        'total',
        'discount',
        'pajak',
        'grandtotal',
        'status'
    ];

    protected $casts = [
        'tglpenerimaan' => 'date',
        'jatuhtempo' => 'date',
        'kurs' => 'decimal:4',
        'total' => 'decimal:4',
        'discount' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grandtotal' => 'decimal:4'
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

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(MSupplier::class, ['kodedivisi', 'kodesupplier'], ['kodedivisi', 'kodesupplier']);
    }

    public function details()
    {
        return $this->hasMany(PartPenerimaanDetail::class, ['kodedivisi', 'nopenerimaan'], ['kodedivisi', 'nopenerimaan']);
    }

    // Scopes
    public function scopeBySupplier($query, $kodeDivisi, $kodeSupplier)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodesupplier', $kodeSupplier);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglpenerimaan', [$startDate, $endDate]);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'Open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'Close');
    }

    // Helper methods
    public function isOpen()
    {
        return $this->status === 'Open';
    }

    public function isClosed()
    {
        return $this->status === 'Close';
    }

    public function getTotalWithTax()
    {
        return $this->total + $this->pajak - $this->discount;
    }

    public function getDiscountPercentage()
    {
        return $this->total > 0 ? ($this->discount / $this->total) * 100 : 0;
    }
}
