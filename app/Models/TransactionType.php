<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionType extends Model
{
    protected $table = 'm_trans';
    protected $primaryKey = 'kode_trans';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_trans',
        'transaksi'
    ];

    public function detailTransactions(): HasMany
    {
        return $this->hasMany(DetailTransaction::class, 'kode_trans', 'kode_trans');
    }
}
