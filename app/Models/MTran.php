<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MTran extends Model
{
    protected $table = 'm_trans';
    
    protected $primaryKey = 'kodetrans';
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'transaksi'
    ];
    
    // Add relationships here as needed
}
