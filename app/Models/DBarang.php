<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DBarang extends Model
{
    protected $table = 'dbo.d_barang';
    
    // No auto-incrementing primary key since we have composite key
    public $incrementing = false;
    
    // Primary key is composite (kodedivisi, kodebarang, id)
    protected $primaryKey = ['kodedivisi', 'kodebarang', 'id'];
    
    // Disable timestamps since the table doesn't have created_at/updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'kodedivisi',
        'kodebarang', 
        'tglmasuk',
        'modal',
        'stok',
        'id'
    ];
    
    protected $casts = [
        'tglmasuk' => 'datetime',
        'modal' => 'decimal:4',
        'stok' => 'integer',
        'id' => 'integer'
    ];
}
