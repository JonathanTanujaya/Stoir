<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBank extends Model
{
    use HasFactory;

    protected $table = 'dbo.d_bank';
    protected $primaryKey = ['KodeDivisi', 'NoRekening'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoRekening',
        'KodeBank',
        'AtasNama',
        'Saldo',
        'Status',
    ];
}
