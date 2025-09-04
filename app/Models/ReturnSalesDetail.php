<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnSalesDetail extends Model
{
    use HasFactory;

    protected $table = 'return_sales_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRetur',
        'NoInvoice',
        'KodeBarang',
        'QtyRetur',
        'HargaNett',
        'Status',
    ];
}
