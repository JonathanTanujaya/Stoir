<?php
// File: app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'INVOICE';
    protected $primaryKey = ['KODE_DIVISI', 'NO_INVOICE'];
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KODE_DIVISI',
        'NO_INVOICE',
        'TGL_INVOICE',
        'KODE_CUST',
        'KODE_SALES',
        'TIPE',
        'JATUH_TEMPO',
        'TOTAL',
        'DISC',
        'PAJAK',
        'GRAND_TOTAL',
        'SISA_INVOICE',
        'KET',
        'STATUS',
        'USERNAME',
        'TT',
        'LUNAS'
    ];

    protected $casts = [
        'TGL_INVOICE' => 'date',
        'JATUH_TEMPO' => 'date',
        'TOTAL' => 'decimal:2',
        'DISC' => 'decimal:2',
        'PAJAK' => 'decimal:2',
        'GRAND_TOTAL' => 'decimal:2',
        'SISA_INVOICE' => 'decimal:2',
        'LUNAS' => 'boolean'
    ];

    // Relationships
    public function details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, ['KODE_DIVISI', 'NO_INVOICE'], ['KODE_DIVISI', 'NO_INVOICE']);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(MasterCustomer::class, 'KODE_CUST', 'KODE_CUST');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(MasterSales::class, 'KODE_SALES', 'KODE_SALES');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(MasterDivisi::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function masterTT(): HasMany
    {
        return $this->hasMany(MasterTT::class, 'NO_REF', 'NO_INVOICE');
    }

    public function returnSales(): HasMany
    {
        return $this->hasMany(ReturnSales::class, 'NO_INVOICE', 'NO_INVOICE');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('STATUS', '!=', 'Cancel');
    }

    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('KODE_DIVISI', $kodeDivisi);
    }

    public function scopeByCustomer($query, $kodeCustomer)
    {
        return $query->where('KODE_CUST', $kodeCustomer);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('TGL_INVOICE', [$startDate, $endDate]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('JATUH_TEMPO', '<', Carbon::now())
            ->where('LUNAS', false)
            ->where('STATUS', '!=', 'Cancel');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('LUNAS', false)
            ->where('STATUS', '!=', 'Cancel');
    }

    // Accessors
    public function getFormattedTglInvoiceAttribute(): string
    {
        return $this->TGL_INVOICE ? $this->TGL_INVOICE->format('d/m/Y') : '';
    }

    public function getFormattedJatuhTempoAttribute(): string
    {
        return $this->JATUH_TEMPO ? $this->JATUH_TEMPO->format('d/m/Y') : '';
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return number_format($this->GRAND_TOTAL, 2, ',', '.');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->JATUH_TEMPO && $this->JATUH_TEMPO->isPast() && !$this->LUNAS;
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->JATUH_TEMPO->diffInDays(Carbon::now());
    }

    // Methods
    public function getTotalQuantity(): int
    {
        return $this->details()->sum('QTY_SUPPLY');
    }

    public function getTotalItems(): int
    {
        return $this->details()->count();
    }

    public function markAsPaid(): bool
    {
        $this->LUNAS = true;
        $this->SISA_INVOICE = 0;
        return $this->save();
    }

    public function cancel(): bool
    {
        $this->STATUS = 'Cancel';
        $this->GRAND_TOTAL = 0;
        $this->SISA_INVOICE = 0;
        return $this->save();
    }

    // Override key methods for composite primary key
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }
}