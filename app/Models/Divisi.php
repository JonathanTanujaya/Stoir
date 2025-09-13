<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    use HasFactory;
    protected $table = 'm_divisi';
    protected $primaryKey = 'kode_divisi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'nama_divisi'
    ];

    public function banks(): HasMany
    {
        return $this->hasMany(Bank::class, 'kode_divisi', 'kode_divisi');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class, 'kode_divisi', 'kode_divisi');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'kode_divisi', 'kode_divisi');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'kode_divisi', 'kode_divisi');
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'kode_divisi', 'kode_divisi');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'kode_divisi', 'kode_divisi');
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'kode_divisi', 'kode_divisi');
    }
}
