<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MResi extends Model
{
    protected $table = 'm_resi';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_resi',
        'tgl_resi',
        'kode_cust',
        'no_rekening_tujuan',
        'nilai',
        'status'
    ];

    protected $casts = [
        'tgl_resi' => 'date',
        'nilai' => 'decimal:2',
        'status' => 'boolean'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function detailBank(): BelongsTo
    {
        return $this->belongsTo(DetailBank::class, 'no_rekening_tujuan', 'no_rekening')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function penerimaanFinanceDetails(): HasMany
    {
        return $this->hasMany(PenerimaanFinanceDetail::class, ['kode_divisi', 'no_resi'], ['kode_divisi', 'no_resi']);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_resi'];
    }
}
