<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'kode_divisi',
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
     * Ensure updates and deletes include both composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('kode_divisi', '=', $this->original['kode_divisi'] ?? $this->getAttribute('kode_divisi'))
              ->where('username', '=', $this->original['username'] ?? $this->getAttribute('username'));

        return $query;
    }

    /**
     * Relationship with Divisi
     */
    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'kode_divisi', 'kode_divisi');
    }
}
