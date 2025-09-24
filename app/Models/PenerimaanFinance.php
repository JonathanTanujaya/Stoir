<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenerimaanFinance extends Model
{
    protected $table = 'penerimaan_finance';
    protected $primaryKey = 'no_penerimaan';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'no_penerimaan',
        'tgl_penerimaan',
        'tipe',
        'no_ref',
        'tgl_ref',
        'tgl_pencairan',
        'bank_ref',
        'no_rek_tujuan',
        'kode_cust',
        'jumlah',
        'keterangan',
        'status',
        'no_voucher'
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'tgl_ref' => 'date',
        'tgl_pencairan' => 'date',
        'jumlah' => 'decimal:2',
        'keterangan' => 'string' // TEXT column
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'no_rek_tujuan', 'no_rekening');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust');
    }

    public function penerimaanFinanceDetails(): HasMany
    {
        return $this->hasMany(PenerimaanFinanceDetail::class, 'no_penerimaan', 'no_penerimaan');
    }

    public function mVoucher(): BelongsTo
    {
        return $this->belongsTo(MVoucher::class, 'no_voucher', 'no_voucher');
    }
}
