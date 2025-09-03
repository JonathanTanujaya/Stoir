<?php
// File: app/Models/MasterKategori.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterKategori extends Model
{
    use HasFactory;

    protected $table = 'M_KATEGORI';
    protected $primaryKey = 'KODE_KATEGORI';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KODE_KATEGORI',
        'NAMA_KATEGORI',
        'KETERANGAN'
    ];

    // Relationships
    public function barang(): HasMany
    {
        return $this->hasMany(MasterBarang::class, 'KATEGORI', 'KODE_KATEGORI');
    }

    // Methods
    public function getTotalBarang(): int
    {
        return $this->barang()->count();
    }

    public function getActiveBarang(): int
    {
        return $this->barang()->where('AKTIF', true)->count();
    }

    public function getTotalStokKategori(): int
    {
        return $this->barang()
            ->join('D_BARANG', 'M_BARANG.KODE_BARANG', '=', 'D_BARANG.KODE_BARANG')
            ->sum('D_BARANG.STOK');
    }
}
