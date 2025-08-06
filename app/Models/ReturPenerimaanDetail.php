<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenerimaanDetail extends Model
{
    use HasFactory;

    protected $table = 'retur_penerimaan_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRetur',
        'NoPenerimaan',
        'KodeBarang',
        'QtyRetur',
        'HargaNett',
        'Status',
    ];
}
