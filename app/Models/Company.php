<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'dbo.company';
    protected $primaryKey = 'companyname';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'companyname',
        'alamat',
        'kota',
        'an',
        'telp',
        'npwp'
    ];
}
