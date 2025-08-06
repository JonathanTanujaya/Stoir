<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';
    protected $primaryKey = ['KodeDivisi', 'NoInvoice'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoice',
        'TglFaktur',
        'KodeCust',
        'KodeSales',
        'Tipe',
        'JatuhTempo',
        'Total',
        'Disc',
        'Pajak',
        'GrandTotal',
        'SisaInvoice',
        'Ket',
        'Status',
        'username',
        'TT',
    ];
}
