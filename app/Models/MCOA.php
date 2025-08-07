<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCOA extends Model
{
    use HasFactory;

    protected $table = 'm_coa';
    protected $primaryKey = 'KodeCOA';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'KodeCOA',
        'NamaCOA',
        'SaldoNormal',
    ];

    protected $casts = [
        'SaldoNormal' => 'decimal:2'
    ];

    // Relationships
    public function journals()
    {
        return $this->hasMany(Journal::class, 'KodeCOA', 'KodeCOA');
    }

    // Scopes
    public function scopeDebet($query)
    {
        return $query->where('SaldoNormal', '>', 0);
    }

    public function scopeKredit($query)
    {
        return $query->where('SaldoNormal', '<', 0);
    }

    public function scopeByType($query, $prefix)
    {
        return $query->where('KodeCOA', 'like', $prefix . '%');
    }

    // Helper methods
    public function isDebet()
    {
        return $this->SaldoNormal > 0;
    }

    public function isKredit()
    {
        return $this->SaldoNormal < 0;
    }

    public function getAccountType()
    {
        $code = substr($this->KodeCOA, 0, 1);
        
        switch ($code) {
            case '1':
                return 'Aset';
            case '2':
                return 'Hutang';
            case '3':
                return 'Modal';
            case '4':
                return 'Pendapatan';
            case '5':
                return 'Biaya';
            default:
                return 'Lainnya';
        }
    }
}
