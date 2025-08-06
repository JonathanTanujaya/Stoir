<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCOA extends Model
{
    use HasFactory;

    protected $table = 'm_coa';
    protected $primaryKey = 'KodeCOA';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeCOA',
        'NamaCOA',
        'SaldoNormal',
    ];
}
