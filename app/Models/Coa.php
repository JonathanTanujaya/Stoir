<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    protected $table = 'm_coa';
    protected $primaryKey = 'kode_coa';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_coa',
        'saldo_normal'
    ];

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class, 'kode_coa', 'kode_coa');
    }
}
