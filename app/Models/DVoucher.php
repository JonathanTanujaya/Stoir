<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DVoucher extends Model
{
    use HasFactory;

    protected $table = 'dbo.d_voucher';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'NoVoucher',
        'NoPenerimaan',
    ];
}
