<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBarang extends Model
{
    use HasFactory;

    protected $table = 'd_barang';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeBarang',
        'TglMasuk',
        'Modal',
        'Stok',
    ];
}
