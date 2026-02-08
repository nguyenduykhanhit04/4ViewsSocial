<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;

    const EXPIRED_AT = 15;
    protected $table = 'user_verifications';

    protected $fillable = [
        'user_id',
        'code',
        'expires_at', // tgian hết hạn mã code
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
