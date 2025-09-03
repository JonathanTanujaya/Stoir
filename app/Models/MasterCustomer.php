<?php
// File: app/Models/MasterCustomer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterCustomer extends Model
{
    use HasFactory;

    protected $table = 'M_CUST';
    protected $primaryKey = 'KODE_CUST';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KODE_CUST',
        'NAMA_CUST',
        'ALAMAT',
        'KOTA',
        'TELEPON',
        'FAX',
        'EMAIL',
        'NPWP',
        'CONTACT_PERSON',
        'HP_CP',
        'JATUH_TEMPO',
        'AKTIF',
        'AREA',
        'KETERANGAN'
    ];

    protected $casts = [
        'JATUH_TEMPO' => 'integer',
        'AKTIF' => 'boolean'
    ];

    // Relationships
    public function area(): BelongsTo
    {
        return $this->belongsTo(MasterArea::class, 'AREA', 'KODE_AREA');
    }

    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class, 'KODE_CUST', 'KODE_CUST');
    }

    public function returnSales(): HasMany
    {
        return $this->hasMany(ReturnSales::class, 'KODE_CUST', 'KODE_CUST');
    }

    public function masterResi(): HasMany
    {
        return $this->hasMany(MasterResi::class, 'KODE_CUST', 'KODE_CUST');
    }

    public function masterTT(): HasMany
    {
        return $this->hasMany(MasterTT::class, 'KODE_CUST', 'KODE_CUST');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('AKTIF', true);
    }

    public function scopeByArea($query, $area)
    {
        return $query->where('AREA', $area);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('KOTA', 'like', "%{$city}%");
    }

    // Accessors
    public function getFormattedJatuhTempoAttribute(): string
    {
        return $this->JATUH_TEMPO . ' hari';
    }

    // Methods
    public function getTotalPiutang(): float
    {
        return $this->invoice()
            ->where('STATUS', '!=', 'Cancel')
            ->sum('GRAND_TOTAL');
    }

    public function getTotalPenjualan(): float
    {
        return $this->invoice()
            ->where('STATUS', '!=', 'Cancel')
            ->sum('GRAND_TOTAL');
    }

    public function getOverdueInvoices()
    {
        $jatuhTempo = now()->subDays($this->JATUH_TEMPO);
        
        return $this->invoice()
            ->where('STATUS', '!=', 'Cancel')
            ->where('TGL_INVOICE', '<', $jatuhTempo)
            ->where('LUNAS', false)
            ->get();
    }
}
