<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasCompositeKey;

class DetailBank extends Model
{
    use HasCompositeKey;
    
    protected $table = 'd_bank';
    public $incrementing = false;
    public $timestamps = true;
    
    protected $primaryKey = ['kode_divisi', 'no_rekening'];
    
    protected $fillable = [
        'kode_divisi',
        'no_rekening',
        'kode_bank',
        'atas_nama',
        'saldo',
        'status'
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'kode_bank', 'kode_bank')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function saldoBanks()
    {
        return $this->hasMany(SaldoBank::class, ['kode_divisi', 'no_rekening'], ['kode_divisi', 'no_rekening']);
    }
}
