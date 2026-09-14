<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Notification
 *
 * Đại diện cho thông báo hoạt động (Like, Comment, Follow...) gửi tới người dùng.
 *
 * @property int $id
 * @property int $user_id (Người nhận thông báo)
 * @property int $sender_id (Người gây ra hành động)
 * @property string $type
 * @property string $content
 * @property int|null $target_id
 * @property bool $is_read
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'content',
        'target_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
