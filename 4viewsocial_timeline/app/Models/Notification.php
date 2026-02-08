<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    const IS_VIEWED = 1;
    const IS_NOT_VIEWED = 0;

    const NOTI_FOLLOW = 0;
    const NOTI_LIKE_POST = 1;
    const NOTI_COMMENT = 2;
    const NOTI_LIKE_STORY = 3;

    const STATUS_WAIT = 0;
    const STATUS_DONE = 1;
    const STATUS_FAIL = 2;

    protected $fillable = [
        'user_id', 'actor_id', 'content', 'is_view', 'status', 'type', 'post_id'
    ];

    public $timestamps = true;

    /* --- Relationships --- */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
