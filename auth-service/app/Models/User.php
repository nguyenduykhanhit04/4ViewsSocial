<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    const ROLE_ADMIN = 0;
    const ROLE_CLIENT = 1;

    const ONLINE_STATUS = 0;
    const OFFLINE_STATUS = 1;

    const STATUS_ACTIVE = 0;
    const STATUS_BANNED = 1;

    protected $fillable = [
        'user_name',
        'full_name',
        'avatar_url',
        'email',
        'password',
        'facebook_url',
        'thread_url',
        'instagram_url',
        'bio',
        'role',
        'online_status',
        'status',
        'login_fail',
        'token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function verifications()
    {
        return $this->hasMany(UserVerification::class, 'user_id');
    }
}
