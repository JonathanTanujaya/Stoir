<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa$ extends Model
{
    protected $table = 'coa$';
    
    // No primary key found
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'kodeakun',
        'namaakun',
        'saldonormal',
        'f4',
        'f5',
        'f6'
    ];
    
    // Add relationships here as needed
}
