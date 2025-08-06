<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanFinance extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_finance';
    protected $primaryKey = ['KodeDivisi', 'NoPenerimaan'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoPenerimaan',
        'TglPenerimaan',
        'Tipe',
        'NoRef',
        'TglRef',
        'TglPencairan',
        'BankRef',
        'NoRekTujuan',
        'KodeCust',
        'Jumlah',
        'Status',
        'NoVoucher',
    ];
}
