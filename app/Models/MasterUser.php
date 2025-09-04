<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class MasterUser extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'master_user';
    
    // Use single key for simplicity during testing
    protected $primaryKey = 'kodedivisi';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodedivisi',
        'username',
        'nama',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        // Don't auto-hash password due to database field length limitation (50 chars)
        // We'll handle password verification manually
    ];

    /**
     * Find user by composite key
     */
    public static function findByCompositeKey($kodeDivisi, $username)
    {
        return self::where('kodedivisi', $kodeDivisi)
                   ->where('username', $username)
                   ->first();
    }

    /**
     * Find user for authentication
     */
    public static function findForAuth($username, $kodeDivisi = null)
    {
        $query = self::where('username', $username);
        
        if ($kodeDivisi) {
            $query->where('kodedivisi', $kodeDivisi);
        }
        
        return $query->first();
    }

    /**
     * Relationship: User modules
     */
    public function userModules()
    {
        return $this->hasMany(UserModule::class, 'kodedivisi', 'kodedivisi')
                    ->where('master_user_modules.username', $this->username);
    }

    /**
     * Get user's accessible modules
     */
    public function getAccessibleModules()
    {
        // Simplified implementation for now
        return [];
    }

    /**
     * Check if user has access to specific module
     */
    public function hasModuleAccess($btid)
    {
        // Simplified implementation for now
        return false;
    }

    /**
     * Check if user is admin (has access to all modules)
     */
    public function isAdmin()
    {
        // Simplified implementation for now - check if user is admin based on username
        return in_array($this->username, ['admin', 'administrator']);
    }

    /**
     * Get user's division
     */
    public function divisi()
    {
        return $this->belongsTo(MDivisi::class, 'kodedivisi', 'kodedivisi');
    }

    /**
     * Set password attribute - store plain text due to DB field limitation
     * In production, consider extending the password field length
     */
    public function setPasswordAttribute($value)
    {
        // For now, store plain text due to database field length (50 chars)
        // BCrypt needs 60 chars minimum
        $this->attributes['password'] = $value;
    }

    /**
     * Verify password against stored value
     */
    public function verifyPassword($password)
    {
        // For now, simple comparison
        // In production with longer password field, use Hash::check()
        return $this->password === $password;
    }

    /**
     * Get full identifier for user
     */
    public function getFullIdentifierAttribute()
    {
        return $this->kodedivisi . '/' . $this->username;
    }
}