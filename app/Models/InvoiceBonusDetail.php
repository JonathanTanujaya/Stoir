<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBonusDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_bonus_detail';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoiceBonus',
        'KodeBarang',
        'QtySupply',
        'HargaNett',
        'Status',
    ];
}
