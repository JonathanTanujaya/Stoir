<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'm_supplier';
    protected $primaryKey = 'kode_supplier';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    
    protected $fillable = [
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

    public function partPenerimaans(): HasMany
    {
        return $this->hasMany(PartPenerimaan::class, 'kode_supplier', 'kode_supplier');
    }

    public function returPenerimaans(): HasMany
    {
        return $this->hasMany(ReturPenerimaan::class, 'kode_supplier', 'kode_supplier');
    }
}
