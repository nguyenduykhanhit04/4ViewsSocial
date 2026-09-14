<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * Đại diện cho người dùng và các tương tác mạng xã hội trong Timeline Service.
 *
 * @property int $id
 * @property string $user_name
 * @property string $full_name
 * @property string $email
 * @property string|null $avatar_url
 * @property string|null $bio
 * @property int $role
 * @property int $online_status
 * @property int $status
 * @property string|null $device_token
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    const ROLE_ADMIN = 0;
    const ROLE_CLIENT = 1;

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
        'device_token',
        'token',
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function follows()
    {
        return $this->hasMany(Follow::class, 'user_id');
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id');
    }

    public function stories()
    {
        return $this->hasMany(Story::class, 'user_id');
    }
}
