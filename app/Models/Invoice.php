<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    
    protected $table = 'invoice';
    protected $primaryKey = 'no_invoice';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'no_invoice',
        'tgl_faktur',
        'kode_cust',
        'kode_sales',
        'tipe',
        'jatuh_tempo',
        'total',
        'disc',
        'pajak',
        'grand_total',
        'sisa_invoice',
        'ket',
        'status',
        'username',
        'tt'
    ];

    protected $casts = [
        'tgl_faktur' => 'date',
        'jatuh_tempo' => 'date',
        'total' => 'decimal:2',
        'disc' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'sisa_invoice' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales');
    }

    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'no_invoice', 'no_invoice');
    }

    public function penerimaanFinanceDetails(): HasMany
    {
        return $this->hasMany(PenerimaanFinanceDetail::class, 'no_invoice', 'no_invoice');
    }

    public function returnSalesDetails(): HasMany
    {
        return $this->hasMany(ReturnSalesDetail::class, 'no_invoice', 'no_invoice');
    }

    public function dtts(): HasMany
    {
        return $this->hasMany(DTT::class, 'no_invoice', 'no_invoice');
    }
}
