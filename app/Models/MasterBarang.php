<?php
// File: app/Models/MasterBarang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterBarang extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = ['kode_divisi', 'kode_barang'];
    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_divisi',
        'kode_barang',
        'nama_barang',
        'kode_kategori',
        'harga_list',
        'harga_jual',
        'satuan',
        'disc1',
        'disc2',
        'merk',
        'barcode',
        'status',
        'lokasi',
        'stok_min'
    ];

    protected $casts = [
        'harga_list' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'disc1' => 'decimal:2',
        'disc2' => 'decimal:2',
        'status' => 'boolean',
        'stok_min' => 'integer'
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

    public function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Relationships
    public function detailBarang(): HasMany
    {
        return $this->hasMany(DetailBarang::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    // Removed MasterKategori relation (model deleted). Reintroduce later if replacement exists.
    public function kategoriDetail(): BelongsTo
    {
        // Placeholder: return empty belongsTo to avoid errors if called.
        return $this->belongsTo(MBarang::class, 'kategori', 'kategori');
    }

    public function kartuStok(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    public function invoiceDetail(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    public function partPenerimaanDetail(): HasMany
    {
        return $this->hasMany(PartPenerimaanDetail::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    public function returnSalesDetail(): HasMany
    {
        return $this->hasMany(ReturnSalesDetail::class, 'KODE_BARANG', 'KODE_BARANG');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('AKTIF', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('KATEGORI', $category);
    }

    // Accessors
    public function getFormattedHargaBeliAttribute(): string
    {
        return number_format($this->HARGA_BELI, 2, ',', '.');
    }

    public function getFormattedHargaJualAttribute(): string
    {
        return number_format($this->HARGA_JUAL, 2, ',', '.');
    }

    // Methods
    public function getTotalStok(): int
    {
        return $this->detailBarang()->sum('STOK');
    }

    public function getStokByDivisi(string $kodeDivisi): int
    {
        return $this->detailBarang()
            ->where('KODE_DIVISI', $kodeDivisi)
            ->sum('STOK');
    }
}
