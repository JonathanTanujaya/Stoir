<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'claim_penjualan_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoClaim',
        'NoInvoice',
        'KodeBarang',
        'QtyClaim',
        'Status',
    ];
}
