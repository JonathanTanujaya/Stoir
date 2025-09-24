<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'master_user';
    public $incrementing = false;
    public $timestamps = false;
    
    protected $primaryKey = 'username';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nama',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // 'password' => 'hashed', // Commented out for testing with production database constraints
        ];
    }

    /**
     * Custom query handling for non-numeric primary key.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('username', '=', $this->original['username'] ?? $this->getAttribute('username'));

        return $query;
    }
}
