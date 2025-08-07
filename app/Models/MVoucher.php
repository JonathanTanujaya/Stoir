<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MVoucher extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_voucher';
    protected $primaryKey = 'novoucher';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'novoucher',
        'tanggal',
        'kodesales',
        'totalomzet',
        'komisi',
        'jumlahkomisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'totalomzet' => 'decimal:4',
        'komisi' => 'decimal:2',
        'jumlahkomisi' => 'decimal:4',
    ];

    /**
     * Relationship: Sales person
     */
    public function sales()
    {
        return $this->belongsTo(MSales::class, 'kodesales', 'kodesales');
    }

    /**
     * Scope: Filter by sales
     */
    public function scopeBySales($query, $kodeSales)
    {
        return $query->where('kodesales', $kodeSales);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by month and year
     */
    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month);
    }

    /**
     * Calculate commission percentage
     */
    public function getCommissionPercentageAttribute()
    {
        if ($this->totalomzet > 0) {
            return ($this->jumlahkomisi / $this->totalomzet) * 100;
        }
        return 0;
    }

    /**
     * Check if voucher has valid commission
     */
    public function hasValidCommission()
    {
        return $this->jumlahkomisi > 0 && $this->totalomzet > 0;
    }
}
