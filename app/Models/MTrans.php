<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MTrans extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_trans';
    protected $primaryKey = 'KodeTrans';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KodeTrans',
        'Transaksi',
    ];

    // Relationships
    public function dTrans()
    {
        return $this->hasMany(DTrans::class, 'KodeTrans', 'KodeTrans');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'KodeTrans', 'KodeTrans');
    }

    // Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('Transaksi', 'like', "%{$name}%");
    }

    // Helper methods
    public function getCOAMappings()
    {
        return $this->dTrans()->with('coa')->get();
    }
}
