<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheLock extends Model
{
    protected $table = 'cache_locks';
    
    protected $primaryKey = ['key', 'key'];
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'owner',
        'expiration'
    ];
    
    // Add relationships here as needed
}
