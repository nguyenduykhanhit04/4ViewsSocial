<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $table = 'follows';

    protected $fillable = ['user_id', 'following_id'];

    public $timestamps = true;

    /* --- Relationships --- */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
