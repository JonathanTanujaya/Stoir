<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DTT extends Model
{
    use HasFactory;

    protected $table = 'dbo.d_tt';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'NoTT',
        'NoRef',
    ];
}
