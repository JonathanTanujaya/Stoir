<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBank extends Model
{
    use HasFactory;

    protected $table = 'm_bank';
    protected $primaryKey = ['KodeDivisi', 'KodeBank'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeBank',
        'NamaBank',
        'Status',
    ];
}
