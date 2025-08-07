<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MSale extends Model
{
    protected $table = 'm_sales';
    
    protected $primaryKey = ['kodedivisi', 'kodesales'];
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'namasales',
        'alamat',
        'nohp',
        'target',
        'status'
    ];
    
    // Add relationships here as needed
}
