<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBarang extends Model
{
    use HasFactory;

    protected $table = 'd_barang';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;
    
    protected $keyType = 'int';

    protected $fillable = [
        'kode_divisi',
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
     * Relationship with Divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    /**
     * Relationship with Barang (master)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    /**
     * Relationship with KartuStok
     */
    public function kartuStoks()
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang')
                    ->where('kode_divisi', $this->kode_divisi);
    }

    /**
     * Get stock movement history for this specific item
     */
    public function stockMovements()
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang')
                    ->where('kode_divisi', $this->kode_divisi)
                    ->orderBy('tgl_proses', 'desc');
    }

    /**
     * Scope to filter by division
     */
    public function scopeForDivision($query, $kodeDivisi)
    {
        return $query->where('kode_divisi', $kodeDivisi);
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
