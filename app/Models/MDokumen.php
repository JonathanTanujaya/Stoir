<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MDokumen extends Model
{
    use HasFactory;

    protected $table = 'm_dokumen';
    protected $primaryKey = ['KodeDivisi', 'KodeDok'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeDok',
        'Nomor',
    ];
}
