<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBarang extends Model
{
    protected $table = 'd_barang';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'stok'
    ];

    protected $casts = [
        'stok' => 'integer'
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
