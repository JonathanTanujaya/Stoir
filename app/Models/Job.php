<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    
    protected $primaryKey = ['id', 'id'];
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'queue',
        'payload',
        'attempts',
        'reserved_at',
        'available_at'
    ];
    
    // Add relationships here as needed
}
