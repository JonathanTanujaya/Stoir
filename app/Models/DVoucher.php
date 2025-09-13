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
        'kode_divisi',
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

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_voucher', 'no_invoice'];
    }
}
