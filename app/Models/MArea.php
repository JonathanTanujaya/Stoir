<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MArea extends Model
{
    use HasFactory;

    protected $table = 'm_area';
    
    // Use first column as primary key for Laravel compatibility
    protected $primaryKey = 'kodedivisi';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'kodearea', 
        'area',
        'status',
    ];
    
    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeArea)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodearea', $kodeArea)
                   ->first();
    }
}
