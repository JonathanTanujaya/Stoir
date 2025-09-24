<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuStok extends Model
{
    protected $table = 'kartu_stok';
    protected $primaryKey = 'urut';
    public $incrementing = true;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_barang',
        'no_ref',
        'tgl_proses',
        'tipe',
        'increase',
        'decrease',
        'harga_debet',
        'harga_kredit',
        'qty',
        'hpp'
    ];

    protected $casts = [
        'tgl_proses' => 'datetime',
        'increase' => 'integer',
        'decrease' => 'integer',
        'harga_debet' => 'decimal:2',
        'harga_kredit' => 'decimal:2',
        'qty' => 'integer',
        'hpp' => 'decimal:2'
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
