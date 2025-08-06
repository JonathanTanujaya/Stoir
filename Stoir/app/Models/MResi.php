<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MResi extends Model
{
    use HasFactory;

    protected $table = 'm_resi';
    protected $primaryKey = ['KodeDivisi', 'NoResi'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoResi',
        'NoRekeningTujuan',
        'TglPembayaran',
        'KodeCust',
        'Jumlah',
        'SisaResi',
        'Keterangan',
        'Status',
    ];
}
