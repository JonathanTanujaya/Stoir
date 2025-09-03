<?php
// File: app/Models/DetailBarang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class DetailBarang extends Model
{
    use HasFactory;

    protected $table = 'D_BARANG';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'KODE_DIVISI',
        'KODE_BARANG',
        'TGL_MASUK',
        'MODAL',
        'STOK'
    ];

    protected $casts = [
        'TGL_MASUK' => 'datetime',
        'MODAL' => 'decimal:2',
        'STOK' => 'integer',
        'ID' => 'integer'
    ];

    // Relationships
    public function masterBarang(): BelongsTo
    {
        return $this->belongsTo(MasterBarang::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(MasterDivisi::class, 'KODE_DIVISI', 'KODE_DIVISI');
    }

    // Scopes
    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('KODE_DIVISI', $kodeDivisi);
    }

    public function scopeHasStock($query)
    {
        return $query->where('STOK', '>', 0);
    }

    public function scopeByBarang($query, $kodeBarang)
    {
        return $query->where('KODE_BARANG', $kodeBarang);
    }

    public function scopeOldestFirst($query)
    {
        return $query->orderBy('TGL_MASUK', 'asc');
    }

    // Accessors
    public function getFormattedModalAttribute(): string
    {
        return number_format($this->MODAL, 2, ',', '.');
    }

    public function getFormattedTglMasukAttribute(): string
    {
        return $this->TGL_MASUK ? $this->TGL_MASUK->format('d/m/Y') : '';
    }

    public function getTotalNilaiAttribute(): float
    {
        return $this->STOK * $this->MODAL;
    }

    // Methods for FIFO calculation
    public function canSupply(int $qtyNeeded): bool
    {
        return $this->STOK >= $qtyNeeded;
    }

    public function reduceStock(int $qty): bool
    {
        if ($this->STOK >= $qty) {
            $this->STOK -= $qty;
            return $this->save();
        }
        return false;
    }

    public function addStock(int $qty): bool
    {
        $this->STOK += $qty;
        return $this->save();
    }
}
