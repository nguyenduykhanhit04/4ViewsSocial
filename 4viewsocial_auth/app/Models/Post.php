<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'user_id', 'caption', 'total_like', 'thumbnail_url', 'total_comment'
    ];

    public $timestamps = true;

    /* --- Relationships --- */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(LikePost::class, 'post_id');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'post_id');
    }
}
