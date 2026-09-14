<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Story
 *
 * Đại diện cho tin 24h (Story) của người dùng.
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $content
 * @property string $media_url
 * @property string $media_type
 * @property \Carbon\Carbon $expires_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Story extends Model
{
    use HasFactory;

    protected $table = 'stories';

    protected $fillable = [
        'user_id',
        'content',
        'media_url',
        'media_type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Tác giả tạo Story.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Danh sách người thích Story này.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(LikeStory::class, 'story_id');
    }
}
