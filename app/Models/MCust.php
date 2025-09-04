<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MCust extends Model
{
    use HasFactory;

    protected $table = 'm_cust';
    
    // Use kodecust as primary key for simplicity
    protected $primaryKey = 'kodecust';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'kodecust',
        'namacust',
        'kodearea',
        'alamat',
        'telp',
        'contact',
        'creditlimit',
        'jatuhtempo',
        'status',
        'nonpwp',
        'namapajak',
        'alamatpajak'
    ];

    protected $casts = [
        'creditlimit' => 'decimal:2',
        'jatuhtempo' => 'integer',
        'status' => 'boolean'
    ];

    // Relationships
    public function area()
    {
        return $this->belongsTo(MArea::class, ['kodedivisi', 'kodearea'], ['kodedivisi', 'kodearea']);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, ['KodeDivisi', 'KodeCust'], ['kodedivisi', 'kodecust']);
    }

    public function sales()
    {
        return $this->belongsTo(MSales::class, ['kodedivisi', 'kodearea'], ['kodedivisi', 'kodearea']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByArea($query, $kodeDivisi, $kodeArea)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodearea', $kodeArea);
    }

    public function scopeWithCreditLimit($query)
    {
        return $query->where('creditlimit', '>', 0);
    }

    // Helper methods
    public function hasReachedCreditLimit($currentDebt)
    {
        return $this->creditlimit > 0 && $currentDebt >= $this->creditlimit;
    }

    public function getRemainingCredit($currentDebt)
    {
        return max(0, $this->creditlimit - $currentDebt);
    }

    public function isPajakCustomer()
    {
        return !empty($this->nonpwp) && !empty($this->namapajak);
    }
    
    // Custom method to find by composite key
    public static function findByCompositeKey($kodeDivisi, $kodeCust)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('kodecust', $kodeCust)
                   ->first();
    }
}
