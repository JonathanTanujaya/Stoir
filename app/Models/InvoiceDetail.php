<?php
// File: app/Models/InvoiceDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $table = 'INVOICE_DETAIL';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'KODE_DIVISI',
        'NO_INVOICE',
        'KODE_BARANG',
        'QTY_SUPPLY',
        'HARGA_JUAL',
        'JENIS',
        'DISKON1',
        'DISKON2',
        'HARGA_NETT',
        'STATUS'
    ];

    protected $casts = [
        'QTY_SUPPLY' => 'integer',
        'HARGA_JUAL' => 'decimal:2',
        'DISKON1' => 'decimal:2',
        'DISKON2' => 'decimal:2',
        'HARGA_NETT' => 'decimal:2',
        'ID' => 'integer'
    ];

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, ['KODE_DIVISI', 'NO_INVOICE'], ['KODE_DIVISI', 'NO_INVOICE']);
    }

    public function masterBarang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(MasterDivisi::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    // Scopes
    public function scopeByInvoice($query, $kodeDivisi, $noInvoice)
    {
        return $query->where('KODE_DIVISI', $kodeDivisi)
            ->where('NO_INVOICE', $noInvoice);
    }

    public function scopeByBarang($query, $kodeBarang)
    {
        return $query->where('KODE_BARANG', $kodeBarang);
    }

    public function scopeActive($query)
    {
        return $query->where('STATUS', '!=', 'Cancel');
    }

    // Accessors
    public function getFormattedHargaJualAttribute(): string
    {
        return number_format($this->HARGA_JUAL, 2, ',', '.');
    }

    public function getFormattedHargaNettAttribute(): string
    {
        return number_format($this->HARGA_NETT, 2, ',', '.');
    }

    public function getTotalDiskonAttribute(): float
    {
        return $this->DISKON1 + $this->DISKON2;
    }

    public function getSubTotalAttribute(): float
    {
        return $this->QTY_SUPPLY * $this->HARGA_NETT;
    }

    // Methods
    public function calculateDiscount(): float
    {
        $discountAmount = 0;
        
        // Calculate discount 1
        if ($this->DISKON1 > 0) {
            $discountAmount += ($this->QTY_SUPPLY * $this->HARGA_JUAL) * ($this->DISKON1 / 100);
        }
        
        // Calculate discount 2 from remaining amount
        if ($this->DISKON2 > 0) {
            $afterDiscount1 = ($this->QTY_SUPPLY * $this->HARGA_JUAL) - $discountAmount;
            $discountAmount += $afterDiscount1 * ($this->DISKON2 / 100);
        }
        
        return $discountAmount;
    }

    public function calculateNettPrice(): float
    {
        $grossAmount = $this->QTY_SUPPLY * $this->HARGA_JUAL;
        $discountAmount = $this->calculateDiscount();
        return $grossAmount - $discountAmount;
    }
}