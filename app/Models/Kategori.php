<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;
    
    protected $table = 'm_kategori';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $primaryKey = 'kode_kategori';
    protected $keyType = 'string';
    
    protected $fillable = [
        'kode_divisi',
        'kode_kategori',
        'kategori',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Ensure updates and deletes include both composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('kode_divisi', '=', $this->original['kode_divisi'] ?? $this->getAttribute('kode_divisi'))
              ->where('kode_kategori', '=', $this->original['kode_kategori'] ?? $this->getAttribute('kode_kategori'));

        return $query;
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'kode_kategori', 'kode_kategori')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function dPakets(): HasMany
    {
        return $this->hasMany(DPaket::class, 'kode_kategori', 'kode_kategori')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
