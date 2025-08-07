<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opname extends Model
{
    use HasFactory;

    protected $table = 'dbo.opname';
    protected $primaryKey = ['kodedivisi', 'noopname'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'noopname',
        'tanggal',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:4',
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
     * Relationship: Opname details
     */
    public function details()
    {
        return $this->hasMany(OpnameDetail::class, ['kodedivisi', 'noopname'], ['kodedivisi', 'noopname']);
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
     * Calculate total from details
     */
    public function calculateTotal()
    {
        return $this->details()->sum('totalvalue');
    }

    /**
     * Get opname summary
     */
    public function getSummary()
    {
        $details = $this->details()->with('barang')->get();
        
        return [
            'total_items' => $details->count(),
            'total_value' => $this->total,
            'calculated_total' => $details->sum('totalvalue'),
            'variance' => $this->total - $details->sum('totalvalue')
        ];
    }
}
