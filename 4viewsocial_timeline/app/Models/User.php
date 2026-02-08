<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    const ROLE_ADMIN = 0;
    const ROLE_CLIENT = 1;

    const ONLINE_STATUS = 0;
    const OFFLINE_STATUS = 1;

    const STATUS_ACTIVE = 0;
    const STATUS_BANNED = 1;
    protected $fillable = [
        'user_name', 'full_name', 'avatar_url', 'email', 'password',
        'facebook_url', 'thread_url', 'instagram_url', 'bio',
        'role', 'online_status', 'status', 'login_fail', 'token', 'device_token'
    ];

    public $timestamps = true;

    /* --- Relationships --- */
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
