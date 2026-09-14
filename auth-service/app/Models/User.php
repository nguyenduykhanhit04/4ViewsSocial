<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * Đại diện cho thực thể Người dùng trong hệ thống xác thực.
 *
 * @property int $id
 * @property string $user_name
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property string|null $avatar_url
 * @property string|null $bio
 * @property int $role
 * @property int $online_status
 * @property int $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Định nghĩa các vai trò người dùng trong hệ thống.
     */
    const ROLE_ADMIN = 0;
    const ROLE_CLIENT = 1;

    /**
     * Trạng thái hoạt động mạng của tài khoản.
     */
    const ONLINE_STATUS = 0;
    const OFFLINE_STATUS = 1;

    /**
     * Trạng thái kích hoạt của tài khoản.
     */
    const STATUS_ACTIVE = 0;
    const STATUS_BANNED = 1;

    /**
     * Các trường được phép gán giá trị hàng loạt (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_name',
        'full_name',
        'avatar_url',
        'email',
        'password',
        'facebook_url',
        'thread_url',
        'instagram_url',
        'bio',
        'role',
        'online_status',
        'status',
        'login_fail',
        'token'
    ];

    /**
     * Các trường bị ẩn khi chuyển đổi sang mảng hoặc JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Quan hệ 1-N: Một người dùng có nhiều mã xác thực OTP.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(UserVerification::class, 'user_id');
    }
}
