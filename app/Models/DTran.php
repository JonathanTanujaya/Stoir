<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DTran extends Model
{
    protected $table = 'd_trans';
    
    // No primary key found
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'kodetrans',
        'kodecoa',
        'saldonormal'
    ];
    
    // Add relationships here as needed
}
