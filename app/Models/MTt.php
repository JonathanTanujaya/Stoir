<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MTt extends Model
{
    protected $table = 'm_tt';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'no_tt',
        'tgl_tt',
        'kode_cust',
        'nilai',
        'status'
    ];

    protected $casts = [
        'tgl_tt' => 'date',
        'nilai' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust');
    }

    public function dTts(): HasMany
    {
        return $this->hasMany(DTt::class, 'no_tt', 'no_tt');
    }

    public function getKeyName(): array
    {
        return ['no_tt'];
    }
}
