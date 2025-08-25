<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBarang extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_barang';
    protected $primaryKey = ['KodeDivisi', 'KodeBarang'];
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KodeDivisi',
        'KodeBarang',
        'NamaBarang',
        'KodeKategori',
        'HargaList',
        'HargaJual',
        'HargaList2',
        'HargaJual2',
        'Satuan',
        'Disc1',
        'Disc2',
        'merk',
        'Barcode',
        'status',
        'Lokasi',
        'StokMin',
        'Checklist'
    ];

    protected $casts = [
        'hargajual' => 'decimal:2',
        'hargabeli' => 'decimal:2',
        'stok_min' => 'integer',
        'aktif' => 'boolean'
    ];

    // Relationships
    public function kategoriDetail()
    {
        return $this->belongsTo(MKategori::class, 'kategori', 'kodekategori');
    }

    public function divisi()
    {
        return $this->belongsTo(MDivisi::class, 'kodedivisi', 'kodedivisi');
    }

    public function stokBarang()
    {
        return $this->hasMany(DBarang::class, 'kodebarang', 'kodebarang');
    }

    public function kartuStok()
    {
        return $this->hasMany(KartuStok::class, 'kodebarang', 'kodebarang');
    }

    // Scope untuk mendapatkan barang aktif saja
    public function scopeActive($query)
    {
        return $query->where('aktif', 1);
    }

    // Scope untuk sorting alphabetical
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('namabarang', 'asc');
    }

    // Scope untuk filter by division
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }
}
