<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sales extends Model
{
    use HasFactory;
    
    protected $table = 'm_sales';
    protected $primaryKey = 'kode_sales';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'kode_sales',
        'nama_sales',
        'kode_area',
        'alamat',
        'no_hp',
        'target',
        'status'
    ];

    protected $casts = [
        'target' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'kode_sales', 'kode_sales');
    }

    public function mVouchers(): HasMany
    {
        return $this->hasMany(MVoucher::class, 'kode_sales', 'kode_sales');
    }
}
