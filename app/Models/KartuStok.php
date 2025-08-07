<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartustok'; // Updated to match database table name
    protected $primaryKey = 'urut';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'KodeBarang',
        'No_Ref',
        'TglProses',
        'Tipe',
        'Increase',
        'Decrease',
        'Harga_Debet',
        'Harga_Kredit',
        'Qty',
        'HPP'
    ];

    protected $casts = [
        'TglProses' => 'date',
        'Increase' => 'decimal:2',
        'Decrease' => 'decimal:2',
        'Harga_Debet' => 'decimal:2',
        'Harga_Kredit' => 'decimal:2',
        'Qty' => 'decimal:2',
        'HPP' => 'decimal:2'
    ];

    // Relationships
    public function barang()
    {
        return $this->belongsTo(MBarang::class, ['KodeDivisi', 'KodeBarang'], ['kodedivisi', 'kodebarang']);
    }

    // Scopes
    public function scopeByBarang($query, $kodeDivisi, $kodeBarang)
    {
        return $query->where('KodeDivisi', $kodeDivisi)
                    ->where('KodeBarang', $kodeBarang);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('TglProses', [$startDate, $endDate]);
    }

    public function scopeStockIn($query)
    {
        return $query->where('Increase', '>', 0);
    }

    public function scopeStockOut($query)
    {
        return $query->where('Decrease', '>', 0);
    }
}
