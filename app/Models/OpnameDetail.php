<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.opname_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noopname',
        'kodebarang',
        'qty',
        'modal',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'modal' => 'decimal:4',
    ];

    /**
     * Relationship: Opname header
     */
    public function opname()
    {
        return $this->belongsTo(Opname::class, ['kodedivisi', 'noopname'], ['kodedivisi', 'noopname']);
    }

    /**
     * Relationship: Barang (item)
     */
    public function barang()
    {
        return $this->belongsTo(DBarang::class, 'kodebarang', 'kodebarang');
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    /**
     * Scope: Filter by opname number
     */
    public function scopeByOpname($query, $noOpname)
    {
        return $query->where('noopname', $noOpname);
    }

    /**
     * Calculate total value
     */
    public function getTotalValueAttribute()
    {
        return $this->qty * $this->modal;
    }

    /**
     * Get variance from system stock
     */
    public function getStockVariance()
    {
        $systemStock = KartuStok::where('kodebarang', $this->kodebarang)
                                ->where('kodedivisi', $this->kodedivisi)
                                ->orderBy('tanggal', 'desc')
                                ->first();
        
        if ($systemStock) {
            return $this->qty - $systemStock->sisa;
        }
        
        return $this->qty; // If no system stock, variance is the opname qty
    }

    /**
     * Check if there's significant variance (>5%)
     */
    public function hasSignificantVariance()
    {
        $variance = abs($this->getStockVariance());
        $threshold = $this->qty * 0.05; // 5% threshold
        
        return $variance > $threshold;
    }
}
