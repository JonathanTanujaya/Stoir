<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenerimaanDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.returpenerimaan_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noretur',
        'nopenerimaan',
        'kodebarang',
        'qtyretur',
        'harganett',
        'status',
    ];

    protected $casts = [
        'qtyretur' => 'decimal:4',
        'harganett' => 'decimal:4',
    ];

    /**
     * Relationship: Retur Penerimaan header
     */
    public function returPenerimaan()
    {
        return $this->belongsTo(ReturPenerimaan::class, ['kodedivisi', 'noretur'], ['kodedivisi', 'noretur']);
    }

    /**
     * Relationship: Part Penerimaan reference
     */
    public function partPenerimaan()
    {
        return $this->belongsTo(PartPenerimaan::class, ['kodedivisi', 'nopenerimaan'], ['kodedivisi', 'nopenerimaan']);
    }

    /**
     * Relationship: Barang (item)
     */
    public function barang()
    {
        return $this->belongsTo(DBarang::class, 'kodebarang', 'kodebarang');
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Calculate total retur value
     */
    public function getTotalReturAttribute()
    {
        return $this->qtyretur * $this->harganett;
    }

    /**
     * Check if return is processed
     */
    public function isProcessed()
    {
        return $this->status === 'Finish';
    }
}
