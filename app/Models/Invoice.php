<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'dbo.invoice';
    protected $primaryKey = ['kodedivisi', 'noinvoice'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noinvoice',
        'tglfaktur',
        'kodecust',
        'kodesales',
        'tipe',
        'jatuhtempo',
        'total',
        'disc',
        'pajak',
        'grandtotal',
        'sisainvoice',
        'ket',
        'status',
        'username',
        'tt'
    ];

    protected $casts = [
        'tglfaktur' => 'date',
        'jatuhtempo' => 'date',
        'total' => 'decimal:2',
        'disc' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grandtotal' => 'decimal:2',
        'sisainvoice' => 'decimal:2',
        'status' => 'boolean'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(MCust::class, ['kodedivisi', 'kodecust'], ['kodedivisi', 'kodecust']);
    }

    public function sales()
    {
        return $this->belongsTo(MSales::class, ['kodedivisi', 'kodesales'], ['kodedivisi', 'kodesales']);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, ['kodedivisi', 'noinvoice'], ['kodedivisi', 'noinvoice']);
    }

    // Scopes
    public function scopeByCustomer($query, $kodeDivisi, $kodeCust)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('kodecust', $kodeCust);
    }

    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('tglfaktur', [$startDate, $endDate]);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('sisainvoice', '>', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('sisainvoice', '<=', 0);
    }

    // Helper methods
    public function isPaid()
    {
        return $this->sisainvoice <= 0;
    }

    public function getDiscountPercentage()
    {
        return $this->total > 0 ? ($this->disc / $this->total) * 100 : 0;
    }
}
