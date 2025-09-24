<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DTrans extends Model
{
    use HasFactory;

    protected $table = 'd_trans';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_trans',
        'kode_coa',
        'saldo_normal',
    ];

    protected $casts = [
        'saldo_normal' => 'string',
    ];

    // Relationship dengan TransactionType (m_trans)
    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class, 'kode_trans', 'kode_trans');
    }

    // Relationship dengan MCoa
    public function coa(): BelongsTo
    {
        return $this->belongsTo(MCoa::class, 'kode_coa', 'kode_coa');
    }
}
