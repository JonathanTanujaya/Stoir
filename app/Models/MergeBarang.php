<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MergeBarang extends Model
{
    use HasFactory;

    protected $table = 'dbo.merge_barang';
    protected $primaryKey = ['kodedivisi', 'nocreate'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'nocreate',
        'tanggal',
        'kodebarang',
        'qty',
        'modal',
        'biayatambahan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'qty' => 'decimal:4',
        'modal' => 'decimal:4',
        'biayatambahan' => 'decimal:4',
    ];

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if(!is_array($keys)){
            return parent::setKeysForSaveQuery($query);
        }

        foreach($keys as $keyName){
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if(is_null($keyName)){
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    /**
     * Relationship: Merge details (components used)
     */
    public function details()
    {
        return $this->hasMany(MergeBarangDetail::class, ['kodedivisi', 'nocreate'], ['kodedivisi', 'nocreate']);
    }

    /**
     * Relationship: Resulting product
     */
    public function barang()
    {
        return $this->belongsTo(DBarang::class, 'kodebarang', 'kodebarang');
    }

    /**
     * Relationship: Division
     */
    public function divisi()
    {
        return $this->belongsTo(MDivisi::class, 'kodedivisi', 'kodedivisi');
    }

    /**
     * Scope: Filter by division
     */
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Calculate total production cost
     */
    public function getTotalCostAttribute()
    {
        $componentsCost = $this->details()->sum('total');
        return $componentsCost + $this->biayatambahan;
    }

    /**
     * Calculate cost per unit
     */
    public function getCostPerUnitAttribute()
    {
        if ($this->qty > 0) {
            return $this->getTotalCostAttribute() / $this->qty;
        }
        return 0;
    }

    /**
     * Get merge summary
     */
    public function getMergeSummary()
    {
        $details = $this->details()->with('barang')->get();
        
        return [
            'result_product' => $this->barang->namabarang ?? 'Unknown',
            'quantity_produced' => $this->qty,
            'components_used' => $details->count(),
            'total_component_cost' => $details->sum('total'),
            'additional_cost' => $this->biayatambahan,
            'total_cost' => $this->getTotalCostAttribute(),
            'cost_per_unit' => $this->getCostPerUnitAttribute()
        ];
    }
}
