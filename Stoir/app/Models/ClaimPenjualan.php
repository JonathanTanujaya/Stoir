<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPenjualan extends Model
{
    use HasFactory;

    protected $table = 'claim_penjualan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoClaim',
        'TglClaim',
        'KodeCust',
        'Keterangan',
        'Status',
    ];
}
