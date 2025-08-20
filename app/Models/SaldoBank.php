<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoBank extends Model
{
    use HasFactory;

    protected $table = 'dbo.saldobank';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'norekening',
        'tglproses',
        'keterangan',
        'debet',
        'kredit',
        'saldo'
    ];

    protected $casts = [
        'tglproses' => 'date',
        'debet' => 'decimal:4',
        'kredit' => 'decimal:4',
        'saldo' => 'decimal:4',
        'id' => 'integer'
    ];

    // Relationships
    public function bank()
    {
        return $this->belongsTo(MBank::class, 'norekening', 'norekening');
    }

    // Scopes
    public function scopeByDivisi($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    public function scopeByRekening($query, $noRekening)
    {
        return $query->where('norekening', $noRekening);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglproses', [$startDate, $endDate]);
    }

    public function scopeDebet($query)
    {
        return $query->where('debet', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('kredit', '>', 0);
    }

    // Helper methods
    public function isDebet()
    {
        return $this->debet > 0;
    }

    public function isKredit()
    {
        return $this->kredit > 0;
    }

    public function getTransactionAmount()
    {
        return $this->debet > 0 ? $this->debet : $this->kredit;
    }

    public function getTransactionType()
    {
        return $this->debet > 0 ? 'Debet' : 'Kredit';
    }
}
