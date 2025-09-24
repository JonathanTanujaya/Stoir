<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DTt extends Model
{
    protected $table = 'd_tt';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'no_tt',
        'no_invoice',
        'tgl_invoice',
        'kode_cust',
        'nilai'
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'nilai' => 'decimal:2'
    ];

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
        return ['no_tt', 'no_invoice'];
    }
}
