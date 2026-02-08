<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikeStory extends Model
{
    protected $table = 'like_stories';

    protected $fillable = [
        'user_id', 'story_id'
    ];

    public $timestamps = true;

    /* --- Relationships --- */
    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
