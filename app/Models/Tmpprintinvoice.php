<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tmpprintinvoice extends Model
{
    protected $table = 'tmpprintinvoice';
    
    // No primary key found
    
    public $incrementing = false;
    
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
