<?php

namespace App\Services;

use App\Models\Follow;
use App\Models\User;

class UserService
{
    /**
     * Gợi ý danh sách bạn bè / người dùng nổi bật.
     *
     * @param  int  $currentUserId
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFriendSuggestions(int $currentUserId, int $limit = 10)
    {
        $followingIds = Follow::where('user_id', $currentUserId)->pluck('following_id')->toArray();
        $followingIds[] = $currentUserId;

        return User::whereNotIn('id', $followingIds)
            ->where('status', User::STATUS_ACTIVE)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Toggle Follow / Unfollow người dùng khác.
     *
     * @param  int  $userId
     * @param  int  $targetUserId
     * @return array
     */
    public function toggleFollow(int $userId, int $targetUserId): array
    {
        if ($userId === $targetUserId) {
            return ['success' => false, 'message' => 'Bạn không thể tự theo dõi chính mình.'];
        }

        $follow = Follow::where('user_id', $userId)->where('following_id', $targetUserId)->first();

        if ($follow) {
            $follow->delete();
            return ['success' => true, 'following' => false, 'message' => 'Đã bỏ theo dõi.'];
        }

        Follow::create([
            'user_id'      => $userId,
            'following_id' => $targetUserId,
        ]);

        return ['success' => true, 'following' => true, 'message' => 'Đã theo dõi thành công.'];
    }

    /**
     * Tìm kiếm người dùng theo từ khóa (username hoặc họ tên).
     *
     * @param  string  $keyword
     * @param  int     $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchUsers(string $keyword, int $limit = 20)
    {
        return User::where('user_name', 'like', "%{$keyword}%")
            ->orWhere('full_name', 'like', "%{$keyword}%")
            ->where('status', User::STATUS_ACTIVE)
            ->limit($limit)
            ->get();
    }
}
