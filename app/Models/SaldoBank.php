<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaldoBank extends Model
{
    protected $table = 'saldo_bank';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_bank',
        'no_rekening',
        'tgl_saldo',
        'saldo_awal',
        'saldo_akhir',
        'keterangan'
    ];

    protected $casts = [
        'tgl_saldo' => 'date',
        'saldo_awal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'kode_bank', 'kode_bank')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function detailBank(): BelongsTo
    {
        return $this->belongsTo(DetailBank::class, 'no_rekening', 'no_rekening')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
