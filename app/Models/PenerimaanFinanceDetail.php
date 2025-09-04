<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanFinanceDetail extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_finance_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'nopenerimaan',
        'noinvoice',
        'jumlahinvoice',
        'sisainvoice',
        'jumlahbayar',
        'jumlahdispensasi',
        'status',
    ];

    protected $casts = [
        'jumlahinvoice' => 'decimal:4',
        'sisainvoice' => 'decimal:4',
        'jumlahbayar' => 'decimal:4',
        'jumlahdispensasi' => 'decimal:4',
        'id' => 'integer'
    ];

    // Relationships (perbaikan untuk composite key)
    public function penerimaanFinance()
    {
        return $this->belongsTo(PenerimaanFinance::class, 'nopenerimaan', 'nopenerimaan')
                    ->where('penerimaanfinance.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'noinvoice', 'noinvoice')
                    ->where('invoice.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    // Scopes
    public function scopeByPenerimaan($query, $kodeDivisi, $noPenerimaan)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('nopenerimaan', $noPenerimaan);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'Finish');
    }

    public function scopeWithDispensasi($query)
    {
        return $query->where('jumlahdispensasi', '>', 0);
    }

    // Helper methods
    public function isFinished()
    {
        return $this->status === 'Finish';
    }

    public function hasDispensasi()
    {
        return $this->jumlahdispensasi > 0;
    }

    public function getNetPayment()
    {
        return $this->jumlahbayar + $this->jumlahdispensasi;
    }
}
