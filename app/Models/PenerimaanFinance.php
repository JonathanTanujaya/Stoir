<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanFinance extends Model
{
    protected $table = 'penerimaan_finance';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_penerimaan_finance',
        'tgl_penerimaan',
        'kode_bank',
        'no_rekening',
        'no_rek_tujuan',
        'kode_cust',
        'nilai',
        'keterangan'
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'nilai' => 'decimal:2'
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

    public function detailBankTujuan(): BelongsTo
    {
        return $this->belongsTo(DetailBank::class, 'no_rek_tujuan', 'no_rekening')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function penerimaanFinanceDetails(): HasMany
    {
        return $this->hasMany(PenerimaanFinanceDetail::class, ['kode_divisi', 'no_penerimaan_finance'], ['kode_divisi', 'no_penerimaan_finance']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_penerimaan_finance'];
    }
}
