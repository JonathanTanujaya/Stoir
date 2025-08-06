<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoice',
        'KodeBarang',
        'QtySupply',
        'HargaJual',
        'Jenis',
        'Diskon1',
        'Diskon2',
        'HargaNett',
        'Status',
    ];
}
