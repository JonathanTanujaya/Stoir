<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MTrans extends Model
{
    use HasFactory;

    protected $table = 'm_trans';
    protected $primaryKey = 'KodeTrans';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeTrans',
        'Transaksi',
    ];
}
