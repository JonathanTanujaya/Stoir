<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MKategori extends Model
{
    use HasFactory;

    protected $table = 'm_kategori';
    protected $primaryKey = ['KodeDivisi', 'KodeKategori'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeKategori',
        'Kategori',
        'Status',
    ];
}
