<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $table = 'journal';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'kode_coa', 'kode_coa');
    }
}
