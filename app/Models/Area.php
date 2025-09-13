<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $table = 'm_area';
    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = 'kode_area';
    protected $keyType = 'string';

    protected $fillable = [
        'kode_divisi',
        'kode_area',
        'area',
        'status',
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
              ->where('kode_area', '=', $this->original['kode_area'] ?? $this->getAttribute('kode_area'));

        return $query;
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'kode_area', 'kode_area')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'kode_area', 'kode_area')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
