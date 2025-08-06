<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MSales extends Model
{
    use HasFactory;

    protected $table = 'm_sales';
    protected $primaryKey = ['KodeDivisi', 'KodeSales'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeSales',
        'NamaSales',
        'Alamat',
        'NoHP',
        'Target',
        'Status',
    ];
}
