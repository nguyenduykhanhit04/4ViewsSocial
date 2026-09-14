<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Post
 *
 * Đại diện cho bài viết trên bảng tin của người dùng.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $caption
 * @property string|null $thumbnail_url
 * @property int $total_like
 * @property int $total_comment
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'caption',
        'total_like',
        'thumbnail_url',
        'total_comment',
    ];

    public $timestamps = true;

    /**
     * Tác giả của bài viết.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Danh sách bình luận của bài viết.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id');
    }

    /**
     * Danh sách lượt thích bài viết.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(LikePost::class, 'post_id');
    }

    /**
     * Danh sách người dùng lưu bài viết (Bookmark/Favourite).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function favourites(): HasMany
    {
        return $this->hasMany(Favourite::class, 'post_id');
    }
}
