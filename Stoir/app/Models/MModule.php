<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MModule extends Model
{
    use HasFactory;

    protected $table = 'm_module';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'NamaModule',
        'btID',
        'Modal',
    ];
}
