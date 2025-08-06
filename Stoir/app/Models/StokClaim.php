<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokClaim extends Model
{
    use HasFactory;

    protected $table = 'stok_claim';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeBarang',
        'StokClaim',
        'Modal',
    ];
}
