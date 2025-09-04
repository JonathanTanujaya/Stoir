<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    
    protected $fillable = [
        'invoice_number',
        'receipt_date',
        'due_date',
        'supplier_id',
        'total',
        'global_discount',
        'dpp',
        'tax',
        'tax_percent',
        'grand_total',
        'note',
        'status'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2',
        'global_discount' => 'decimal:2',
        'dpp' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'grand_total' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(MSupplier::class, 'supplier_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
