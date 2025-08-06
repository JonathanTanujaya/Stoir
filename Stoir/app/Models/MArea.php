<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MArea extends Model
{
    use HasFactory;

    protected $table = 'm_area';
    protected $primaryKey = ['KodeDivisi', 'KodeArea'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeArea',
        'Area',
        'status',
    ];
}
