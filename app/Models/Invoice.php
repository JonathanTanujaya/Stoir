<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice';
    protected $primaryKey = ['KodeDivisi', 'NoInvoice'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoice',
        'TglFaktur',
        'KodeCust',
        'KodeSales',
        'Tipe',
        'JatuhTempo',
        'Total',
        'Disc',
        'Pajak',
        'GrandTotal',
        'SisaInvoice',
        'Ket',
        'Status',
        'username',
        'TT'
    ];

    protected $casts = [
        'TglFaktur' => 'date',
        'JatuhTempo' => 'date',
        'Total' => 'decimal:2',
        'Disc' => 'decimal:2',
        'Pajak' => 'decimal:2',
        'GrandTotal' => 'decimal:2',
        'SisaInvoice' => 'decimal:2',
        'Status' => 'boolean'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(MCust::class, ['KodeDivisi', 'KodeCust'], ['kodedivisi', 'kodecust']);
    }

    public function sales()
    {
        return $this->belongsTo(MSales::class, ['KodeDivisi', 'KodeSales'], ['kodedivisi', 'kodesales']);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, ['KodeDivisi', 'NoInvoice'], ['KodeDivisi', 'NoInvoice']);
    }

    // Scopes
    public function scopeByCustomer($query, $kodeDivisi, $kodeCust)
    {
        return $query->where('KodeDivisi', $kodeDivisi)
                    ->where('KodeCust', $kodeCust);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('TglFaktur', [$startDate, $endDate]);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('SisaInvoice', '>', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('SisaInvoice', '<=', 0);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->SisaInvoice <= 0;
    }

    public function getDiscountPercentage()
    {
        return $this->Total > 0 ? ($this->Disc / $this->Total) * 100 : 0;
    }
}
