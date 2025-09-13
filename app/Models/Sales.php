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
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = 'kode_sales';
    protected $keyType = 'string';
    
    protected $fillable = [
        'kode_divisi',
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

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function area(): BelongsTo
    {
        // Note: We cannot safely constrain by kode_divisi here using whereColumn
        // because it breaks eager loading (no join context). Constrain in queries
        // via eager load closures where the kode_divisi is known.
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi)
            ->withoutGlobalScopes();
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getRouteKeyName(): string
    {
        return 'kode_sales';
    }

    /**
     * Ensure updates and deletes include both composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('kode_divisi', '=', $this->original['kode_divisi'] ?? $this->getAttribute('kode_divisi'))
              ->where('kode_sales', '=', $this->original['kode_sales'] ?? $this->getAttribute('kode_sales'));

        return $query;
    }
}
