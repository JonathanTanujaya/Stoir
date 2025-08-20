<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnSales extends Model
{
    use HasFactory;

    protected $table = 'dbo.return_sales';
    protected $primaryKey = ['KodeDivisi', 'NoRetur'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRetur',
        'TglRetur',
        'KodeCust',
        'Total',
        'SisaRetur',
        'Keterangan',
        'Status',
        'TT',
    ];
}
