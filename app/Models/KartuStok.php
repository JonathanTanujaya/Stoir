<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuStok extends Model
{
    protected $table = 'kartu_stok';
    protected $primaryKey = 'urut';
    public $timestamps = false;
    
    protected $fillable = [
        'hpp'
    ];

    protected $casts = [
        'hpp' => 'decimal:2'
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
