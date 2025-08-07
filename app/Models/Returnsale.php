<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Returnsale extends Model
{
    protected $table = 'returnsales';
    
    // No primary key found
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'kodedivisi',
        'noretur',
        'tglretur',
        'kodecust',
        'total',
        'sisaretur',
        'keterangan',
        'status',
        'tt'
    ];
    
    // Add relationships here as needed
}
