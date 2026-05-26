<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'role',
        'public_key',
        'private_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'private_key', // Sembunyikan private key
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class, 'user_id', 'id_user');
    }

    /**
     * Generate RSA Key Pair menggunakan pure PHP (tanpa command line)
     */
    

    public function getAuthIdentifierName()
    {
        return 'id_user';
    }
}
