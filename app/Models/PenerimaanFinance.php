<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanFinance extends Model
{
    use HasFactory;

    protected $table = 'dbo.penerimaanfinance';
    protected $primaryKey = ['kodedivisi', 'nopenerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'nopenerimaan',
        'tglpenerimaan',
        'tipe',
        'noref',
        'tglref',
        'tglpencairan',
        'bankref',
        'norektujuan',
        'kodecust',
        'jumlah',
        'status',
        'novoucher',
    ];

    protected $casts = [
        'tglpenerimaan' => 'date',
        'tglref' => 'date',
        'tglpencairan' => 'date',
        'jumlah' => 'decimal:4'
    ];

    // Relationships (perbaikan untuk composite key)
    public function customer()
    {
        return $this->belongsTo(MCust::class, 'kodecust', 'kodecust')
                    ->where('dbo.m_cust.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    public function sales()
    {
        return $this->belongsTo(MSales::class, 'kodesales', 'kodesales')
                    ->where('dbo.m_sales.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    public function details()
    {
        return $this->hasMany(PenerimaanFinanceDetail::class, 'nopenerimaan', 'nopenerimaan')
                    ->where('dbo.penerimaanfinance_detail.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    // Scopes
    public function scopeByCustomer($query, $kodeDivisi, $kodeCust)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodecust', $kodeCust);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglpenerimaan', [$startDate, $endDate]);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'Finish');
    }

    public function scopeByType($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    // Helper methods
    public function isFinished()
    {
        return $this->status === 'Finish';
    }

    public function getTotalDetails()
    {
        return $this->details()->sum('nominal');
    }
}
