<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DTrans extends Model
{
    use HasFactory;

    protected $table = 'd_trans';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeTrans',
        'KodeCOA',
        'SaldoNormal',
    ];
}
