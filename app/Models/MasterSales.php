<?php
// File: app/Models/MasterSales.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSales extends Model
{
    use HasFactory;

    protected $table = 'M_SALES';
    protected $primaryKey = 'KODE_SALES';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KODE_SALES',
        'NAMA_SALES',
        'ALAMAT',
        'TELEPON',
        'EMAIL',
        'KOMISI',
        'AKTIF'
    ];

    protected $casts = [
        'KOMISI' => 'decimal:2',
        'AKTIF' => 'boolean'
    ];

    // Relationships
    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class, 'KODE_SALES', 'KODE_SALES');
    }

    public function masterVoucher(): HasMany
    {
        return $this->hasMany(MasterVoucher::class, 'KODE_SALES', 'KODE_SALES');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('AKTIF', true);
    }

    // Accessors
    public function getFormattedKomisiAttribute(): string
    {
        return $this->KOMISI . '%';
    }

    // Methods
    public function getTotalPenjualan(): float
    {
        return $this->invoice()
            ->where('STATUS', '!=', 'Cancel')
            ->sum('GRAND_TOTAL');
    }

    public function getTotalKomisi(): float
    {
        $totalPenjualan = $this->getTotalPenjualan();
        return $totalPenjualan * ($this->KOMISI / 100);
    }

    public function getPenjualanByPeriod($startDate, $endDate): float
    {
        return $this->invoice()
            ->where('STATUS', '!=', 'Cancel')
            ->whereBetween('TGL_INVOICE', [$startDate, $endDate])
            ->sum('GRAND_TOTAL');
    }

    public function getKomisiByPeriod($startDate, $endDate): float
    {
        $penjualan = $this->getPenjualanByPeriod($startDate, $endDate);
        return $penjualan * ($this->KOMISI / 100);
    }
}
