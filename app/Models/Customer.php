<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    
    protected $table = 'm_cust';
    protected $primaryKey = 'kode_cust';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    const CREATED_AT = 'created_at';
    
    protected $fillable = [
        'kode_cust',
        'nama_cust',
        'kode_area',
        'alamat',
        'telp',
        'contact',
        'credit_limit',
        'jatuh_tempo',
        'status',
        'no_npwp',
        'nama_pajak',
        'alamat_pajak',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'jatuh_tempo' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'kode_cust', 'kode_cust');
    }

    public function returnSales(): HasMany
    {
        return $this->hasMany(ReturnSales::class, 'kode_cust', 'kode_cust');
    }

    public function mTts(): HasMany
    {
        return $this->hasMany(MTT::class, 'kode_cust', 'kode_cust');
    }

    public function penerimaanFinances(): HasMany
    {
        return $this->hasMany(PenerimaanFinance::class, 'kode_cust', 'kode_cust');
    }
}
