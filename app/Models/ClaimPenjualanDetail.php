<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimPenjualanDetail extends Model
{
    use HasFactory;

    protected $table = 'dbo.claim_penjualan_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noclaim',
        'noinvoice',
        'kodebarang',
        'qtyclaim',
        'status',
    ];

    protected $casts = [
        'qtyclaim' => 'decimal:4',
    ];

    /**
     * Relationship: Claim header
     */
    public function claimPenjualan()
    {
        return $this->belongsTo(ClaimPenjualan::class, ['kodedivisi', 'noclaim'], ['kodedivisi', 'noclaim']);
    }

    /**
     * Relationship: Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, ['kodedivisi', 'noinvoice'], ['kodedivisi', 'noinvoice']);
    }

    /**
     * Relationship: Barang (item)
     */
    public function barang()
    {
        return $this->belongsTo(DBarang::class, 'kodebarang', 'kodebarang');
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
     * Scope: Filter by claim number
     */
    public function scopeByClaim($query, $noClaim)
    {
        return $query->where('noclaim', $noClaim);
    }

    /**
     * Calculate claim value based on invoice detail
     */
    public function getClaimValueAttribute()
    {
        $invoiceDetail = $this->invoice ? 
            $this->invoice->details()->where('kodebarang', $this->kodebarang)->first() : 
            null;
            
        if ($invoiceDetail) {
            return $this->qtyclaim * $invoiceDetail->harganett;
        }
        
        return 0;
    }

    /**
     * Check if claim is approved
     */
    public function isApproved()
    {
        return $this->status === 'Approved';
    }

    /**
     * Get claim percentage against original quantity
     */
    public function getClaimPercentage()
    {
        $invoiceDetail = $this->invoice ? 
            $this->invoice->details()->where('kodebarang', $this->kodebarang)->first() : 
            null;
            
        if ($invoiceDetail && $invoiceDetail->qty > 0) {
            return ($this->qtyclaim / $invoiceDetail->qty) * 100;
        }
        
        return 0;
    }
}
