<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCustDiskonDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_cust_diskon_detail';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeCust',
        'KodeBarang',
        'Qtymin',
        'QtyMax',
        'diskon',
        'Diskon1',
        'Diskon2',
        'jenis',
    ];
}
