<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartu_stok';
    protected $primaryKey = 'urut';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'kodebarang',
        'no_ref',
        'tglproses',
        'tipe',
        'increase',
        'decrease',
        'harga_debet',
        'harga_kredit',
        'qty',
        'hpp'
    ];

    protected $casts = [
        'tglproses' => 'datetime',
        'increase' => 'decimal:4',
        'decrease' => 'decimal:4',
        'harga_debet' => 'decimal:4',
        'harga_kredit' => 'decimal:4',
        'qty' => 'decimal:4',
        'hpp' => 'decimal:4'
    ];

    // Relationships
    public function barang()
    {
        return $this->belongsTo(MBarang::class, 'kodebarang', 'kodebarang');
    }

    // Scopes
    public function scopeByBarang($query, $kodeBarang)
    {
        return $query->where('kodebarang', $kodeBarang);
    }

    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglproses', [$startDate, $endDate]);
    }

    public function scopeByTransactionType($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeMasuk($query)
    {
        return $query->where('increase', '>', 0);
    }

    public function scopeKeluar($query)
    {
        return $query->where('decrease', '>', 0);
    }

    // Helper methods
    public function isMasuk()
    {
        return $this->increase > 0;
    }

    public function isKeluar()
    {
        return $this->decrease > 0;
    }

    public function getNetMovement()
    {
        return $this->increase - $this->decrease;
    }

    public function getTotalValueDebet()
    {
        return $this->qty * $this->harga_debet;
    }

    public function getTotalValueKredit()
    {
        return $this->qty * $this->harga_kredit;
    }
}
