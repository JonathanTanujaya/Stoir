<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMinimum extends Model
{
    protected $table = 'stok_minimum';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_barang',
        'stok_minimum',
        'status_monitoring'
    ];

    protected $casts = [
        'stok_minimum' => 'integer',
        'status_monitoring' => 'boolean'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, ['kode_divisi', 'kode_barang'], ['kode_divisi', 'kode_barang']);
    }
}
