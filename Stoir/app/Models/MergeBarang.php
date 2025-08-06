<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MergeBarang extends Model
{
    use HasFactory;

    protected $table = 'merge_barang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoCreate',
        'Tanggal',
        'KodeBarang',
        'Qty',
        'Modal',
        'BiayaTambahan',
    ];
}
