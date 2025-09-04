<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBarang extends Model
{
    use HasFactory;

    protected $table = 'd_barang';
    protected $primaryKey = ['kodedivisi', 'kodebarang'];
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'kodebarang',
        'tglmasuk',
        'modal',
        'stok',
        'id'
    ];

    protected $casts = [
        'tglmasuk' => 'datetime',
        'modal' => 'decimal:4',
        'stok' => 'integer',
        'id' => 'integer'
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
