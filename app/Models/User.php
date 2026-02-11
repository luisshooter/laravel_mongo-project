<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'permission_level',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'active' => 'boolean',
        'permission_level' => 'integer',
    ];

    // Permission levels
    const PERMISSION_USER = 1;
    const PERMISSION_MANAGER = 2;
    const PERMISSION_ADMIN = 3;

    /**
     * Set the user's password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Check if user has maximum permission
     */
    public function hasMaxPermission(): bool
    {
        return $this->permission_level === self::PERMISSION_ADMIN;
    }

    /**
     * Check if user can access orders
     */
    public function canAccessOrders(): bool
    {
        return $this->permission_level < self::PERMISSION_ADMIN;
    }

    /**
     * Get permission level name
     */
    public function getPermissionNameAttribute(): string
    {
        return match($this->permission_level) {
            self::PERMISSION_USER => 'Usuário',
            self::PERMISSION_MANAGER => 'Gerente',
            self::PERMISSION_ADMIN => 'Administrador',
            default => 'Desconhecido',
        };
    }

    /**
     * Relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
