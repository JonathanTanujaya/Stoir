<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MCoa extends Model
{
    use HasFactory;

    protected $table = 'm_coa';
    protected $primaryKey = 'kode_coa';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_coa',
        'nama_coa',
        'saldo_normal',
    ];

    protected $casts = [
        'saldo_normal' => 'string',
    ];

    // Relationship dengan journal
    public function journals()
    {
        return $this->hasMany(Journal::class, 'kode_coa', 'kode_coa');
    }

    // Relationship dengan d_trans
    public function detailTransactions(): HasMany
    {
        return $this->hasMany(DTrans::class, 'kode_coa', 'kode_coa');
    }
}
