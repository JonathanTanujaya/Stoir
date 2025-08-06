<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartu_stok';
    protected $primaryKey = 'urut';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeBarang',
        'No_Ref',
        'TglProses',
        'Tipe',
        'Increase',
        'Decrease',
        'Harga_Debet',
        'Harga_Kredit',
        'Qty',
        'HPP',
    ];
}
