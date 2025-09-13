<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCompositeKey;

class Customer extends Model
{
    use HasFactory, HasCompositeKey;
    
    protected $table = 'm_cust';
    public $incrementing = false;
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    
    protected $fillable = [
        'kode_divisi',
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
        'kode_sales'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function returnSales(): HasMany
    {
        return $this->hasMany(ReturnSales::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function mTts(): HasMany
    {
        return $this->hasMany(MTt::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function mResis(): HasMany
    {
        return $this->hasMany(MResi::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function penerimaanFinances(): HasMany
    {
        return $this->hasMany(PenerimaanFinance::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'kode_cust'];
    }

    // Override getKey() to return the composite key as an array
    public function getKey()
    {
        $attributes = [];
        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }
        return $attributes; // Return as an associative array
    }

    // Override setKeysForSaveQuery() to handle composite keys
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    /**
     * Get the value of the model's route key.
     */
    public function getRouteKeyName()
    {
        return 'kode_cust'; // Use single field for route key name
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $kodeDivisi = $this->getRouteKeyByName('kode_divisi') ?? request()->route('kode_divisi');
        
        return $this->where('kode_divisi', $kodeDivisi)
                   ->where($this->getRouteKeyName(), $value)
                   ->first();
    }
}
