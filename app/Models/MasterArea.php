<?php
// File: app/Models/MasterArea.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterArea extends Model
{
    use HasFactory;

    protected $table = 'M_AREA';
    protected $primaryKey = 'KODE_AREA';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KODE_AREA',
        'NAMA_AREA',
        'KETERANGAN'
    ];

    // Relationships
    public function customers(): HasMany
    {
        return $this->hasMany(MasterCustomer::class, 'AREA', 'KODE_AREA');
    }

    // Methods
    public function getTotalCustomers(): int
    {
        return $this->customers()->count();
    }

    public function getActiveCustomers(): int
    {
        return $this->customers()->where('AKTIF', true)->count();
    }

    public function getTotalPenjualanArea(): float
    {
        return $this->customers()
            ->join('INVOICE', 'M_CUST.KODE_CUST', '=', 'INVOICE.KODE_CUST')
            ->where('INVOICE.STATUS', '!=', 'Cancel')
            ->sum('INVOICE.GRAND_TOTAL');
    }
}
