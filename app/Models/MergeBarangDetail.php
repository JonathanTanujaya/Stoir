<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MergeBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.merge_barang_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoCreate',
        'KodeBarang',
        'Qty',
        'Modal',
    ];
}
