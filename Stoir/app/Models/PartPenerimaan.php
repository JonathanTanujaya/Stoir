<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'part_penerimaan';
    protected $primaryKey = ['KodeDivisi', 'NoPenerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaan',
        'TglPenerimaan',
        'KodeValas',
        'Kurs',
        'KodeSupplier',
        'JatuhTempo',
        'NoFaktur',
        'Total',
        'Discount',
        'Pajak',
        'GrandTotal',
        'Status',
    ];
}
