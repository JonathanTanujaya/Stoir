<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCompositeKey;

class Invoice extends Model
{
    use HasFactory, HasCompositeKey;
    
    protected $table = 'invoice';
    public $incrementing = false;
    public $timestamps = true;
    
    protected $fillable = [
        'kode_divisi',
        'no_invoice',
        'tgl_faktur',
        'kode_cust',
        'kode_sales',
        'tipe',
        'jatuh_tempo',
        'total',
        'disc',
        'pajak',
        'grand_total',
        'sisa_invoice',
        'ket',
        'status',
        'username',
        'tt'
    ];

    protected $casts = [
        'tgl_faktur' => 'date',
        'jatuh_tempo' => 'date',
        'total' => 'decimal:2',
        'disc' => 'decimal:2',
        'pajak' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'sisa_invoice' => 'decimal:2'
    ];

    // No soft deletes column in schema; keep hard deletes only

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'kode_cust', 'kode_cust')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'kode_sales', 'kode_sales')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'no_invoice', 'no_invoice')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getRouteKeyName(): string
    {
        return 'no_invoice';
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'no_invoice'];
    }

    public function getKey()
    {
        return [
            'kode_divisi' => $this->getAttribute('kode_divisi'),
            'no_invoice' => $this->getAttribute('no_invoice')
        ];
    }

    /**
     * Ensure updates target the correct row when using composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            $query->where($key, '=', $this->getAttribute($key));
        }
        return $query;
    }

    // Deletions are hard deletes; use controller's cancel() to cancel invoices
}
