<?php

namespace App\Services;

use App\Models\Favourite;
use App\Models\Follow;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    /**
     * Lấy thông tin chi tiết hồ sơ người dùng kèm số lượng Follower/Following/Post.
     *
     * @param  int  $userId
     * @param  int|null  $currentUserId
     * @return array
     */
    public function getProfile(int $userId, ?int $currentUserId = null): ?array
    {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $totalPosts = Post::where('user_id', $userId)->count();
        $totalFollowers = Follow::where('following_id', $userId)->count();
        $totalFollowing = Follow::where('user_id', $userId)->count();

        $isFollowing = false;
        if ($currentUserId && $currentUserId !== $userId) {
            $isFollowing = Follow::where('user_id', $currentUserId)->where('following_id', $userId)->exists();
        }

        return [
            'user'            => $user,
            'total_posts'     => $totalPosts,
            'total_followers' => $totalFollowers,
            'total_following' => $totalFollowing,
            'is_following'    => $isFollowing,
        ];
    }

    /**
     * Lấy danh sách bài viết đã lưu (Bookmarks).
     *
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSavedPosts(int $userId)
    {
        $postIds = Favourite::where('user_id', $userId)->pluck('post_id');
        return Post::with(['user', 'comments.user'])->whereIn('id', $postIds)->latest()->get();
    }

    /**
     * Cập nhật thông tin trang cá nhân (Bio, Avatar, Liên kết MXH).
     *
     * @param  \App\Models\User  $user
     * @param  array             $data
     * @param  mixed             $avatarFile
     * @return \App\Models\User
     */
    public function updateProfile(User $user, array $data, $avatarFile = null): User
    {
        if ($avatarFile) {
            $path = $avatarFile->store('avatars', 'public');
            $user->avatar_url = Storage::url($path);
        }

        if (isset($data['full_name'])) {
            $user->full_name = $data['full_name'];
        }
        if (isset($data['bio'])) {
            $user->bio = $data['bio'];
        }
        if (isset($data['facebook_url'])) {
            $user->facebook_url = $data['facebook_url'];
        }
        if (isset($data['thread_url'])) {
            $user->thread_url = $data['thread_url'];
        }
        if (isset($data['instagram_url'])) {
            $user->instagram_url = $data['instagram_url'];
        }

        $user->save();
        return $user;
    }

    /**
     * Đổi mật khẩu tài khoản.
     *
     * @param  \App\Models\User  $user
     * @param  string            $currentPassword
     * @param  string            $newPassword
     * @return array
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không chính xác.'];
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return ['success' => true, 'message' => 'Đổi mật khẩu thành công.'];
    }
}
