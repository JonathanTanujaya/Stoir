<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSupplier extends Model
{
    use HasFactory;

    protected $table = 'm_supplier';
    protected $primaryKey = ['KodeDivisi', 'KodeSupplier'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeSupplier',
        'NamaSupplier',
        'Alamat',
        'Telp',
        'contact',
        'status',
    ];
}
