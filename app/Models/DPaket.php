<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DPaket extends Model
{
    use HasFactory;

    protected $table = 'd_paket';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodePaket',
        'KodeKategori',
        'QtyMin',
        'QtyMax',
        'Diskon1',
        'Diskon2',
    ];
}
