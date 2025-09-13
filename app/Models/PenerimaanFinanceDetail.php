<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerimaanFinanceDetail extends Model
{
    protected $table = 'penerimaan_finance_detail';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_penerimaan',
        'no_invoice',
        'jumlah_invoice',
        'sisa_invoice',
        'jumlah_bayar',
        'jumlah_dispensasi',
        'status'
    ];

    protected $casts = [
        'id' => 'int',
        'jumlah_invoice' => 'decimal:2',
        'sisa_invoice' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'jumlah_dispensasi' => 'decimal:2'
    ];

    public function penerimaanFinance(): BelongsTo
    {
        return $this->belongsTo(PenerimaanFinance::class, 'no_penerimaan', 'no_penerimaan')
                    ->where('penerimaan_finance.kode_divisi', $this->kode_divisi);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice')
                    ->where('invoice.kode_divisi', $this->kode_divisi);
    }
}
