<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MTT extends Model
{
    use HasFactory;

    protected $table = 'm_tt';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoTT',
        'Tanggal',
        'KodeCust',
        'Keterangan',
    ];
}
