<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MDokuman extends Model
{
    protected $table = 'm_dokumen';
    
    protected $primaryKey = ['kodedivisi', 'kodedok'];
    
    public $incrementing = false;
    
    public $timestamps = false;
    
    protected $fillable = [
        'nomor'
    ];
    
    // Add relationships here as needed
}
