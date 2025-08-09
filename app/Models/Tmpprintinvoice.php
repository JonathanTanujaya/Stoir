<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TmpPrintInvoice extends Model
{
    protected $table = 'dbo.tmpprintinvoice';
    protected $primaryKey = 'noinvoice';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'noinvoice',
        'tglfaktur',
        'kodecust',
        'namacust',
        'kodesales',
        'namasales',
        'kodedivisi',
        'kodearea',
        'area',
        'tipe',
        'jatuhtempo',
        'grandtotal',
        'kodebarang',
        'namabarang',
        'qtysupply',
        'hargajual',
        'jenis',
        'diskon1',
        'diskon2',
        'harganett',
        'merk',
        'alamatcust',
        'total',
        'disc',
        'pajak',
        'satuan',
        'username'
    ];
    
    // Add relationships here as needed
}
