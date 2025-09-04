<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MModule extends Model
{
    use HasFactory;

    protected $table = 'm_module';
    protected $primaryKey = 'btid';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'namamodule',
        'btid',
        'modal',
    ];

    protected $casts = [
        'modal' => 'boolean',
    ];

    /**
     * Relationship: User modules that reference this module
     */
    public function userModules()
    {
        return $this->hasMany(UserModule::class, 'btid', 'btid');
    }

    /**
     * Scope: Get active modules (modal = true)
     */
    public function scopeActiveModules($query)
    {
        return $query->where('modal', true);
    }

    /**
     * Scope: Search by module name
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where('namamodule', 'like', '%' . $name . '%');
    }

    /**
     * Get users who have access to this module
     */
    public function getAuthorizedUsers($kodeDivisi = null)
    {
        $query = $this->userModules()->with('user');
        
        if ($kodeDivisi) {
            $query->where('kodedivisi', $kodeDivisi);
        }
        
        return $query->get();
    }

    /**
     * Check if module is accessible in specific division
     */
    public function isAccessibleInDivision($kodeDivisi)
    {
        return $this->userModules()
                   ->where('kodedivisi', $kodeDivisi)
                   ->where('modal', true)
                   ->exists();
    }
}
