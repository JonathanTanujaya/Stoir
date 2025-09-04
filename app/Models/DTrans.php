<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DTrans extends Model
{
    use HasFactory;

    protected $table = 'd_trans';
    protected $primaryKey = ['KodeTrans', 'KodeCOA'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeTrans',
        'KodeCOA',
        'SaldoNormal',
    ];

    protected $casts = [
        'SaldoNormal' => 'decimal:2'
    ];

    // Relationships
    public function mTrans()
    {
        return $this->belongsTo(MTrans::class, 'KodeTrans', 'KodeTrans');
    }

    public function coa()
    {
        return $this->belongsTo(MCOA::class, 'KodeCOA', 'KodeCOA');
    }

    // Scopes
    public function scopeByTrans($query, $kodeTrans)
    {
        return $query->where('KodeTrans', $kodeTrans);
    }

    public function scopeDebet($query)
    {
        return $query->where('SaldoNormal', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('SaldoNormal', '<', 0);
    }
}
