<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenerimaan extends Model
{
    use HasFactory;

    protected $table = 'retur_penerimaan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRetur',
        'TglRetur',
        'KodeSupplier',
        'Total',
        'SisaRetur',
        'Keterangan',
        'Status',
    ];
}
