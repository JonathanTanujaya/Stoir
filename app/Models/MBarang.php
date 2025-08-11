<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MBarang extends Model
{
    use HasFactory;

    protected $table = 'dbo.d_barang';
    
    // Use id as primary key since it's the auto-increment field
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'kodedivisi',
        'kodebarang',
        'tglmasuk',
        'modal',
        'stok',
        'id'
    ];

    protected $casts = [
        'tglmasuk' => 'datetime',
        'modal' => 'decimal:4',
        'stok' => 'integer',
        'id' => 'integer'
    ];

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(MKategori::class, 'kodedivisi', 'kodedivisi');
    }

    public function kartuStok()
    {
        return $this->hasMany(KartuStok::class, 'kodebarang', 'kodebarang');
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'KodeBarang', 'kodebarang');
    }

    // Scopes
    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    public function scopeHasStock($query)
    {
        return $query->where('stok', '>', 0);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglmasuk', [$startDate, $endDate]);
    }

    // Helper methods
    public function hasStock()
    {
        return $this->stok > 0;
    }

    public function getStockValue()
    {
        return $this->stok * $this->modal;
    }

    public function isLowStock($minStock = 10)
    {
        return $this->stok <= $minStock;
    }
    
    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeBarang)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodebarang', $kodeBarang)
                   ->first();
    }
}
