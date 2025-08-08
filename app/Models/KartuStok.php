<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'dbo.kartustok';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'kodebarang',
        'tanggal',
        'noreferensi',
        'jenistransaksi',
        'masuk',
        'keluar',
        'saldo',
        'harga',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'masuk' => 'decimal:4',
        'keluar' => 'decimal:4',
        'saldo' => 'decimal:4',
        'harga' => 'decimal:4'
    ];

    // Relationships
    public function barang()
    {
        return $this->belongsTo(MBarang::class, 'kodebarang', 'kodebarang')
                    ->where('dbo.m_barang.kodedivisi', '=', $this->kodedivisi ?? '');
    }

    // Scopes
    public function scopeByBarang($query, $kodeDivisi, $kodeBarang)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodebarang', $kodeBarang);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeByTransactionType($query, $jenisTransaksi)
    {
        return $query->where('jenistransaksi', $jenisTransaksi);
    }

    public function scopeMasuk($query)
    {
        return $query->where('masuk', '>', 0);
    }

    public function scopeKeluar($query)
    {
        return $query->where('keluar', '>', 0);
    }

    // Helper methods
    public function isMasuk()
    {
        return $this->masuk > 0;
    }

    public function isKeluar()
    {
        return $this->keluar > 0;
    }

    public function getNetMovement()
    {
        return $this->masuk - $this->keluar;
    }

    public function getTotalValue()
    {
        return $this->saldo * $this->harga;
    }
}
