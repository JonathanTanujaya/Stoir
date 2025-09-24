<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DVoucher extends Model
{
    protected $table = 'd_voucher';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'no_voucher',
        'kode_sales',
        'no_invoice',
        'tgl_invoice',
        'kode_cust',
        'nilai'
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'nilai' => 'decimal:2'
    ];

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice');
    }

    public function getKeyName(): array
    {
        return ['no_voucher', 'no_invoice'];
    }
}
