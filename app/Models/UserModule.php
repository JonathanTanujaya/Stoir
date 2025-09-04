<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserModule extends Model
{
    use HasFactory;

    protected $table = 'user_module';
    protected $primaryKey = ['kodedivisi', 'username', 'btid'];
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kodedivisi',
        'username', 
        'btid',
        'modal',
    ];

    protected $casts = [
        'modal' => 'boolean',
    ];

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
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
     *
     * @param mixed $keyName
     * @return mixed
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
     * Relationship: User that owns this module access
     */
    public function user()
    {
        return $this->belongsTo(MasterUser::class, ['kodedivisi', 'username'], ['kodedivisi', 'username']);
    }

    /**
     * Relationship: Module definition
     */
    public function module()
    {
        return $this->belongsTo(MModule::class, 'btid', 'btid');
    }

    /**
     * Scope: Get modules for specific user
     */
    public function scopeForUser($query, $kodeDivisi, $username)
    {
        return $query->where('kodedivisi', $kodeDivisi)
                    ->where('username', $username);
    }

    /**
     * Scope: Get active modules (modal = true)
     */
    public function scopeActiveModules($query)
    {
        return $query->where('modal', true);
    }

    /**
     * Scope: Get by division
     */
    public function scopeByDivision($query, $kodeDivisi)
    {
        return $query->where('kodedivisi', $kodeDivisi);
    }
}
