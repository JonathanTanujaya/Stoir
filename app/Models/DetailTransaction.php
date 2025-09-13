<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaction extends Model
{
    protected $table = 'd_trans';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_trans',
        'saldo_normal'
    ];

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'kode_trans', 'kode_trans');
    }

    public function getKeyName(): array
    {
        return ['kode_trans'];
    }
}
