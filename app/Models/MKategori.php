<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MKategori extends Model
{
    use HasFactory;

    protected $table = 'm_kategori';
    
    // Use first column as primary key for Laravel compatibility
    protected $primaryKey = 'kodedivisi';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'kodekategori',
        'kategori',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Relationships
    public function barang()
    {
        return $this->hasMany(MBarang::class, ['kodedivisi', 'kodekategori'], ['kodedivisi', 'kodekategori']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    // Helper methods
    public function getBarangCount()
    {
        return $this->barang()->count();
    }

    public function getActiveBarangCount()
    {
        return $this->barang()->active()->count();
    }
    
    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeKategori)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodekategori', $kodeKategori)
                   ->first();
    }
}