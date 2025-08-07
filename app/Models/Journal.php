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
        'Transaksi',
        'KodeCOA',
        'NamaCOA',
        'Keterangan',
        'Debet',
        'Kredit'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'Debet' => 'decimal:2',
        'Kredit' => 'decimal:2'
    ];

    // Relationships
    public function coa()
    {
        return $this->belongsTo(MCOA::class, 'KodeCOA', 'kodecoa');
    }

    // Scopes for financial reporting
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    public function scopeDebet($query)
    {
        return $query->where('Debet', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('Kredit', '>', 0);
    }

    public function scopeByCOA($query, $kodeCOA)
    {
        return $query->where('KodeCOA', $kodeCOA);
    }
}
