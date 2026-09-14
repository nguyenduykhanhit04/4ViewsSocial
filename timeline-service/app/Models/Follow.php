<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Follow
 *
 * Đại diện cho mối quan hệ theo dõi giữa hai người dùng.
 *
 * @property int $id
 * @property int $user_id (Người theo dõi - follower)
 * @property int $following_id (Người được theo dõi)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Follow extends Model
{
    use HasFactory;

    protected $table = 'follows';

    protected $fillable = [
        'user_id',
        'following_id',
    ];

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
