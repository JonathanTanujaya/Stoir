<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBank extends Model
{
    use HasFactory;

    protected $table = 'd_bank';
    protected $primaryKey = ['kodedivisi', 'kodebank'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'kodebank',
        'norekening',
        'atasnama',
        'saldo',
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
    public static function findByCompositeKey($kodeDivisi, $kodeBank)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodebank', $kodeBank)
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
