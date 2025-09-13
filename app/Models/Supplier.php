<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'm_supplier';
    protected $primaryKey = 'kode_supplier';
    
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = [
        'kode_divisi',
        'kode_supplier',
        'nama_supplier',
        'alamat',
        'telp',
        'contact',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * Ensure updates and deletes include both composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('kode_divisi', '=', $this->original['kode_divisi'] ?? $this->getAttribute('kode_divisi'))
              ->where('kode_supplier', '=', $this->original['kode_supplier'] ?? $this->getAttribute('kode_supplier'));

        return $query;
    }

    /**
     * Resolve the model for a given route key.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $divisi = request()->route('divisi');
        $field = $field ?? 'kode_supplier';

        return $this->where('kode_divisi', $divisi)
                    ->where($field, $value)
                    ->first();
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function partPenerimaans(): HasMany
    {
        return $this->hasMany(PartPenerimaan::class, 'kode_supplier', 'kode_supplier')
            ->where('kode_divisi', $this->kode_divisi);
    }
}
