<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDokumen extends Model
{
    use HasFactory;

    protected $table = 'm_dokumen';
    
    // Use composite primary key
    protected $primaryKey = ['kodedivisi', 'kodedok'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'kodedok',
        'nomor',
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
    public static function findByCompositeKey($kodeDivisi, $kodeDok)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodedok', $kodeDok)
                   ->first();
    }
}
