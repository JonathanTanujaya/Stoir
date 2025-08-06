<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class COA extends Model
{
    use HasFactory;

    protected $table = 'coa';
    public $timestamps = false;

    protected $fillable = [
        'Kode_Akun',
        'Nama_Akun',
        'Saldo_Normal',
        'F4',
        'F5',
        'F6',
    ];
}
