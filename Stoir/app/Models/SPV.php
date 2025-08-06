<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SPV extends Model
{
    use HasFactory;

    protected $table = 'spv';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'Password',
    ];
}
