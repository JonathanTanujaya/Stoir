<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank';
    protected $primaryKey = 'no_rekening';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_rekening',
        'atas_nama',
        'kode_bank',
        'nama_bank',
        'saldo',
        'status_rekening',
        'status_bank',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'status_bank' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan saldo bank
    public function saldoBanks()
    {
        return $this->hasMany(SaldoBank::class, 'no_rekening', 'no_rekening');
    }

    // Relationship dengan penerimaan finance
    public function penerimaanFinances()
    {
        return $this->hasMany(PenerimaanFinance::class, 'no_rek_tujuan', 'no_rekening');
    }
}
