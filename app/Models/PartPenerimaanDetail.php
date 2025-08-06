<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaanDetail extends Model
{
    use HasFactory;

    protected $table = 'part_penerimaan_detail';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaan',
        'KodeBarang',
        'QtySupply',
        'Harga',
        'Diskon1',
        'Diskon2',
        'HargaNett',
    ];
}
