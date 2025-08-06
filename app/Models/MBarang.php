<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBarang extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = ['KodeDivisi', 'KodeBarang'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeBarang',
        'NamaBarang',
        'KodeKategori',
        'HargaList',
        'HargaJual',
        'HargaList2',
        'HargaJual2',
        'Satuan',
        'Disc1',
        'Disc2',
        'merk',
        'Barcode',
        'status',
        'Lokasi',
        'StokMin',
        'Checklist',
    ];
}
