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
        'kode_divisi',
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

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function dVouchers(): HasMany
    {
        return $this->hasMany(DVoucher::class, ['kode_divisi', 'no_voucher'], ['kode_divisi', 'no_voucher']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_voucher'];
    }
}
