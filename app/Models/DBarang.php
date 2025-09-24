<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DBarang extends Model
{
    use HasFactory;

    protected $table = 'd_barang';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_barang',
        'tgl_masuk',
        'modal',
        'stok',
    ];

    protected $casts = [
        'tgl_masuk' => 'datetime',
        'modal' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Relationship with Barang (master)
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Relationship with KartuStok
     */
    public function kartuStoks(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang');
    }

    /**
     * Get stock movement history for this specific item
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang')
                    ->orderBy('tgl_proses', 'desc');
    }

    /**
     * Scope to filter by product code
     */
    public function scopeForBarang($query, $kodeBarang)
    {
        return $query->where('kode_barang', $kodeBarang);
    }

    /**
     * Scope to filter by stock availability
     */
    public function scopeAvailable($query)
    {
        return $query->where('stok', '>', 0);
    }

    /**
     * Scope to filter by modal price range
     */
    public function scopeModalBetween($query, $min, $max)
    {
        return $query->whereBetween('modal', [$min, $max]);
    }
}
