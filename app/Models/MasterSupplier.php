<?php
// File: app/Models/MasterSupplier.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterSupplier extends Model
{
    use HasFactory;

    protected $table = 'm_supplier';
    protected $primaryKey = ['kode_divisi', 'kode_supplier'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_divisi',
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'telp',
        'contact',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Override methods for composite keys
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[$key] = $this->getAttribute($key);
        }
        return $keys;
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Relationships
    public function divisi()
    {
        return $this->belongsTo(MasterDivisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function partPenerimaan(): HasMany
    {
        return $this->hasMany(PartPenerimaan::class, ['kode_divisi', 'kode_supplier'], ['kode_divisi', 'kode_supplier']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('kode_divisi', $kodeDivisi);
    }

    // Methods
    public function getTotalPembelian(): float
    {
        return $this->partPenerimaan()
            ->where('status', '!=', 'Cancel')
            ->sum('grand_total');
    }

    public function isActive(): bool
    {
        return $this->status === true;
    }
}
