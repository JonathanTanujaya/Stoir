<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.invoice_detail';
    protected $primaryKey = null; // No primary key for composite key tables
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoice',
        'NoUrut',
        'KodeBarang',
        'NamaBarang',
        'Qty',
        'Unit',
        'HargaPokok',
        'HargaJual',
        'Diskon',
        'SubTotal',
        'TotalPokok',
        'Ket'
    ];

    protected $casts = [
        'Qty' => 'decimal:2',
        'HargaPokok' => 'decimal:2',
        'HargaJual' => 'decimal:2',
        'Diskon' => 'decimal:2',
        'SubTotal' => 'decimal:2',
        'TotalPokok' => 'decimal:2'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, ['KodeDivisi', 'NoInvoice'], ['KodeDivisi', 'NoInvoice']);
    }

    public function barang()
    {
        return $this->belongsTo(MBarang::class, 'KodeBarang', 'kodebarang');
    }

    // Scopes
    public function scopeByInvoice($query, $kodeDivisi, $noInvoice)
    {
        return $query->where('KodeDivisi', $kodeDivisi)
                    ->where('NoInvoice', $noInvoice);
    }

    public function scopeByBarang($query, $kodeBarang)
    {
        return $query->where('KodeBarang', $kodeBarang);
    }

    // Helper methods
    public function getNetPrice()
    {
        return $this->HargaJual - $this->Diskon;
    }

    public function getDiscountPercentage()
    {
        return $this->HargaJual > 0 ? ($this->Diskon / $this->HargaJual) * 100 : 0;
    }

    public function getTotalWithoutDiscount()
    {
        return $this->Qty * $this->HargaJual;
    }
}
