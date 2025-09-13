<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    protected $table = 'm_bank';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_bank'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function detailBanks(): HasMany
    {
        return $this->hasMany(DetailBank::class, 'kode_bank', 'kode_bank')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'kode_bank'];
    }
}
