<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnSales extends Model
{
    protected $table = 'return_sales';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_return_sales',
        'tgl_return',
        'kode_cust',
        'no_invoice',
        'nilai',
        'keterangan'
    ];

    protected $casts = [
        'tgl_return' => 'date',
        'nilai' => 'decimal:2'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
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

    public function returnSalesDetails(): HasMany
    {
        return $this->hasMany(ReturnSalesDetail::class, ['kode_divisi', 'no_return_sales'], ['kode_divisi', 'no_return_sales']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_return_sales'];
    }
}
