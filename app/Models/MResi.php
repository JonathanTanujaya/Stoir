<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MResi extends Model
{
    use HasFactory;

    protected $table = 'dbo.m_resi';
    protected $primaryKey = ['kodedivisi', 'noresi'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noresi',
        'norekeningtujuan',
        'tglpembayaran',
        'kodecust',
        'jumlah',
        'sisaresi',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'tglpembayaran' => 'date',
        'jumlah' => 'decimal:4',
        'sisaresi' => 'decimal:4'
    ];

    // Override methods for composite keys
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    public function getKey()
    {
        $keys = [];
        foreach ($this->getKeyName() as $key) {
            $keys[$key] = $this->getAttribute($key);
        }
        return $keys;
    }

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(MCust::class, ['kodedivisi', 'kodecust'], ['kodedivisi', 'kodecust']);
    }

    // Scopes
    public function scopeByCustomer($query, $kodeDivisi, $kodeCust)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodecust', $kodeCust);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglpembayaran', [$startDate, $endDate]);
    }

    public function scopeFinished($query)
    {
        return $query->where('status', 'Finish');
    }

    public function scopeHasSisa($query)
    {
        return $query->where('sisaresi', '>', 0);
    }

    // Helper methods
    public function isFinished()
    {
        return $this->status === 'Finish';
    }

    public function hasSisa()
    {
        return $this->sisaresi > 0;
    }

    public function getTotalPaid()
    {
        return $this->jumlah - $this->sisaresi;
    }
}
