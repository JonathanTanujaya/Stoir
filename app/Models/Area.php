<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    use HasFactory;

    protected $table = 'm_area';
    protected $primaryKey = 'kode_area';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_area',
        'area',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'kode_area', 'kode_area');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sales::class, 'kode_area', 'kode_area');
    }
}
