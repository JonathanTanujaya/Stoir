<?php
// File: app/Models/MasterDivisi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterDivisi extends Model
{
    use HasFactory;

    protected $table = 'm_divisi';
    protected $primaryKey = 'kode_divisi';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_divisi',
        'nama_divisi',
        'alamat',
        'telepon',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean'
    ];

    // Relationships
    public function detailBarang(): HasMany
    {
        return $this->hasMany(DetailBarang::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function partPenerimaan(): HasMany
    {
        return $this->hasMany(PartPenerimaan::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function kartuStok(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function opname(): HasMany
    {
        return $this->hasMany(Opname::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function mergeBarang(): HasMany
    {
        return $this->hasMany(MergeBarang::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    public function returnSales(): HasMany
    {
        return $this->hasMany(ReturnSales::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('AKTIF', true);
    }

    // Methods
    public function getTotalStokBarang(): int
    {
        return $this->detailBarang()->sum('STOK');
    }

    public function getTotalNilaiStok(): float
    {
        return $this->detailBarang()->get()->sum(function ($item) {
            return $item->STOK * $item->MODAL;
        });
    }
}
