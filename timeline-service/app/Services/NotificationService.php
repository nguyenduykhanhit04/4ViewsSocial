<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Lấy danh sách thông báo của người dùng.
     *
     * @param  int  $userId
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserNotifications(int $userId, int $limit = 30)
    {
        return Notification::with('sender')
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Đánh dấu tất cả thông báo của người dùng là đã đọc.
     *
     * @param  int  $userId
     * @return int  Số lượng thông báo đã cập nhật
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Cập nhật Firebase Device Token (FCM) của người dùng để nhận thông báo đẩy.
     *
     * @param  int     $userId
     * @param  string  $deviceToken
     * @return bool
     */
    public function updateDeviceToken(int $userId, string $deviceToken): bool
    {
        return (bool) User::where('id', $userId)->update(['device_token' => $deviceToken]);
    }
}
