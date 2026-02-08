<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $table = 'stories';

    protected $fillable = [
        'video_url', 'user_id', 'expired_time'
    ];

    public $timestamps = true;

    /* --- Relationships --- */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(LikeStory::class, 'story_id');
    }
}
