<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSupplier extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_supplier';
    protected $primaryKey = ['kodedivisi', 'kodesupplier'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'kodesupplier',
        'namasupplier',
        'alamat',
        'telp',
        'contact',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    // Override getKeyName for composite keys
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    // Override getKey for composite keys
    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[$key] = $this->getAttribute($key);
        }
        return $keys;
    }

    // Override setKeysForSaveQuery for composite keys
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeSupplier)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodesupplier', $kodeSupplier)
                   ->first();
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
}
