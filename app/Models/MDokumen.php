<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MDokumen extends Model
{
    protected $table = 'm_dokumen';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_trans',
        'nomor',
        'prefix',
        'tahun',
        'bulan'
    ];

    protected $casts = [
        'nomor' => 'integer',
        'tahun' => 'integer',
        'bulan' => 'integer'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'kode_trans', 'kode_trans');
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'kode_trans'];
    }
}
