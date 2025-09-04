<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSales extends Model
{
    use HasFactory;

    protected $table = 'm_sales';
    
    // Use first column as primary key for Laravel compatibility
    protected $primaryKey = 'kodedivisi';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'kodesales',
        'namasales',
        'alamat',
        'nohp',
        'target',
        'status',
    ];
    
    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeSales)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodesales', $kodeSales)
                   ->first();
    }
}
