<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'dbo.company';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'namacompany',
        'alamat',
        'kota',
        'telp',
        'fax',
        'email',
        'website',
        'npwp',
        'direktur'
    ];
}
