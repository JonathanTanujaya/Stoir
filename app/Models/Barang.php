<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'm_barang';
    public $incrementing = false;
    protected $primaryKey = 'kode_barang';
    protected $keyType = 'string';
    
    protected $fillable = [
        'kode_divisi',
        'kode_barang',
        'nama_barang',
        'kode_kategori',
        'harga_list',
        'harga_jual',
        'satuan',
        'disc1',
        'disc2',
        'merk',
        'barcode',
        'status',
        'lokasi',
        'stok_min'
    ];

    protected $casts = [
        'harga_list' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'disc1' => 'decimal:2',
        'disc2' => 'decimal:2',
        'status' => 'boolean',
        'stok_min' => 'integer'
    ];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kode_kategori', 'kode_kategori');
    }

    public function detailBarang(): HasOne
    {
        return $this->hasOne(DetailBarang::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function kartuStoks(): HasMany
    {
        return $this->hasMany(KartuStok::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    public function invoiceDetails(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'kode_barang', 'kode_barang')
            ->where('kode_divisi', $this->kode_divisi);
    }

    /**
     * Set the keys for a save operation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('kode_divisi', $this->getAttribute('kode_divisi'))
              ->where('kode_barang', $this->getAttribute('kode_barang'));

        return $query;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return [
            'kode_divisi' => $this->getAttribute('kode_divisi'),
            'kode_barang' => $this->getAttribute('kode_barang')
        ];
    }
}
