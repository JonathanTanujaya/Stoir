<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceBonus extends Model
{
    use HasFactory;

    protected $table = 'invoice_bonus';
    protected $primaryKey = ['KodeDivisi', 'NoInvoiceBonus'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'KodeDivisi',
        'NoInvoiceBonus',
        'TglFaktur',
        'KodeCust',
        'Ket',
        'Status',
        'username',
    ];
}
