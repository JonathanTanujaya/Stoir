<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MVoucher extends Model
{
    protected $table = 'm_voucher';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'no_voucher',
        'tgl_voucher',
        'kode_sales',
        'nilai',
        'status'
    ];

    protected $casts = [
        'tgl_voucher' => 'date',
        'nilai' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales');
    }

    public function dVouchers(): HasMany
    {
        return $this->hasMany(DVoucher::class, 'no_voucher', 'no_voucher');
    }

    public function getKeyName(): array
    {
        return ['no_voucher'];
    }
}
