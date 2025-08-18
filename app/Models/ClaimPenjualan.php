<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPenjualan extends Model
{
    use HasFactory;

    protected $table = 'dbo.claim_penjualan';
    protected $primaryKey = ['kodedivisi', 'noclaim'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noclaim',
        'tglclaim',
        'kodecust',
        'keterangan',
        'status',
    ];

    /**
     * The relationships that should always be loaded.
     */
    protected $with = ['details', 'customer', 'divisi', 'hasOneThrough', 'hasManyThrough'];

    protected $casts = [
        'tglclaim' => 'date',
    ];

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    /**
     * Relationship: Claim details
     */
    public function details()
    {
        return $this->hasMany(ClaimPenjualanDetail::class, ['kodedivisi', 'noclaim'], ['kodedivisi', 'noclaim']);
    }

    /**
     * Relationship: Customer
     */
    public function customer()
    {
        return $this->belongsTo(MCust::class, 'kodecust', 'kodecust');
    }

    /**
     * Relationship: Division
     */
    public function divisi()
    {
        return $this->belongsTo(MDivisi::class, 'kodedivisi', 'kodedivisi');
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglclaim', [$startDate, $endDate]);
    }

    /**
     * Calculate total claim amount
     */
    public function getTotalClaimAmount()
    {
        return $this->details()->sum('totalclaim');
    }

    /**
     * Check if claim is processed
     */
    public function isProcessed()
    {
        return $this->status === 'Processed';
    }
}
