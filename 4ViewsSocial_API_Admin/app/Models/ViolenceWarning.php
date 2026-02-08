<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolenceWarning extends Model
{
    protected $table = 'violence_warnings';

    protected $fillable = [
        'user_id', 'infringe_id'
    ];

    public $timestamps = false;

    /* --- Relationships --- */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function infringe()
    {
        return $this->belongsTo(User::class, 'infringe_id');
    }
}
