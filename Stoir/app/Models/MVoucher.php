<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MVoucher extends Model
{
    use HasFactory;

    protected $table = 'm_voucher';
    protected $primaryKey = 'NoVoucher';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoVoucher',
        'Tanggal',
        'KodeSales',
        'TotalOmzet',
        'Komisi',
        'JumlahKomisi',
    ];
}
