<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TmpPrintTT extends Model
{
    use HasFactory;

    protected $table = 'dbo.tmp_print_tt';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NoRetur',
    ];
}
