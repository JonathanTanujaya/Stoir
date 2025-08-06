<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCust extends Model
{
    use HasFactory;

    protected $table = 'm_cust';
    protected $primaryKey = ['KodeDivisi', 'KodeCust'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeCust',
        'NamaCust',
        'KodeArea',
        'Alamat',
        'Telp',
        'Contact',
        'CreditLimit',
        'JatuhTempo',
        'Status',
        'NoNPWP',
        'NIK',
        'NamaPajak',
        'AlamatPajak',
    ];
}
