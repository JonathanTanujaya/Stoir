<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;
    
    protected $table = 'm_kategori';
    protected $primaryKey = 'kode_kategori';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'kode_kategori',
        'kategori',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'kode_kategori', 'kode_kategori');
    }

    public function dPakets(): HasMany
    {
        return $this->hasMany(DPaket::class, 'kode_kategori', 'kode_kategori');
    }
}
