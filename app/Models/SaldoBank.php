<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SaldoBank extends Model
{
    protected $table = 'saldo_bank';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
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

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'kode_bank', 'kode_bank');
    }
}
