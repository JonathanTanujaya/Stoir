<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DPaket extends Model
{
    protected $table = 'd_paket';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_paket',
        'kode_barang',
        'kode_kategori',
        'qty'
    ];

    protected $casts = [
        'qty' => 'integer'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function getKeyName(): array
    {
        return ['kode_divisi', 'kode_paket', 'kode_barang'];
    }
}
