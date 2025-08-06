<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanFinanceDetail extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_finance_detail';
    protected $primaryKey = ['KodeDivisi', 'NoPenerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaan',
        'Noinvoice',
        'JumlahInvoice',
        'SisaInvoice',
        'JumlahBayar',
        'JumlahDispensasi',
        'Status',
        'id',
    ];
}
