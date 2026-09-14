<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserVerification
 *
 * Lưu trữ mã xác thực OTP phục vụ kích hoạt tài khoản và xác minh email.
 *
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property \Carbon\Carbon $expires_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class UserVerification extends Model
{
    use HasFactory;

    /**
     * Thời gian mã OTP có hiệu lực (tính theo phút).
     */
    const EXPIRED_AT = 15;

    protected $table = 'user_verifications';

    /**
     * Các trường được phép gán giá trị hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];

    /**
     * Định dạng kiểu dữ liệu tự động cho các thuộc tính.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Quan hệ N-1: Bản ghi xác thực thuộc về một Người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
