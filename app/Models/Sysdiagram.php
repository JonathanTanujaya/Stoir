<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sysdiagram extends Model
{
    protected $table = 'sysdiagrams';
    
    // No primary key found
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'principal_id',
        'diagram_id',
        'version',
        'definition'
    ];
    
    // Add relationships here as needed
}
