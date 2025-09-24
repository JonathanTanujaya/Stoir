<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'm_barang';
    protected $primaryKey = 'kode_barang';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
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
        'stok_min' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori');
    }

    public function dBarangs(): HasMany
    {
        return $this->hasMany(DBarang::class, 'kode_barang', 'kode_barang');
    }

    public function kartuStoks(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang');
    }

    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'kode_barang', 'kode_barang');
    }

    public function partPenerimaanDetails(): HasMany
    {
        return $this->hasMany(PartPenerimaanDetail::class, 'kode_barang', 'kode_barang');
    }

    public function returnSalesDetails(): HasMany
    {
        return $this->hasMany(ReturnSalesDetail::class, 'kode_barang', 'kode_barang');
    }

    public function returPenerimaanDetails(): HasMany
    {
        return $this->hasMany(ReturPenerimaanDetail::class, 'kode_barang', 'kode_barang');
    }

    
}
