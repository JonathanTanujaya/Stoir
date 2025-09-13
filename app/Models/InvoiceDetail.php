<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetail extends Model
{
    use HasFactory;
    
    protected $table = 'invoice_detail';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'no_invoice',
        'kode_barang',
        'qty_supply',
        'harga_jual',
        'jenis',
        'diskon1',
        'diskon2',
        'harga_nett',
        'status'
    ];

    protected $casts = [
        'qty_supply' => 'integer',
        'harga_jual' => 'decimal:2',
        'diskon1' => 'decimal:2',
        'diskon2' => 'decimal:2',
        'harga_nett' => 'decimal:2',
    ];

    /**
     * Relationship with Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'no_invoice', 'no_invoice')
            ->where('kode_divisi', $this->kode_divisi);
    }

    /**
     * Relationship with Barang
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    /**
     * Relationship with Divisi
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }
}
