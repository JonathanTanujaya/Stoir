<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoBank extends Model
{
    use HasFactory;

    protected $table = 'saldo_bank';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRekening',
        'TglProses',
        'Keterangan',
        'Debet',
        'Kredit',
        'Saldo',
    ];
}
