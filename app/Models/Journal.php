<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'journal';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'transaksi',
        'kodecoa',
        'namacoa',
        'keterangan',
        'debet',
        'kredit'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'debet' => 'decimal:2',
        'kredit' => 'decimal:2'
    ];

    // Relationships
    public function coa()
    {
        return $this->belongsTo(MCOA::class, 'kodecoa', 'kodecoa');
    }

    // Scopes for financial reporting
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeDebet($query)
    {
        return $query->where('debet', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('kredit', '>', 0);
    }

    public function scopeByCOA($query, $kodeCOA)
    {
        return $query->where('kodecoa', $kodeCOA);
    }
}
