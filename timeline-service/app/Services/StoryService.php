<?php

namespace App\Services;

use App\Models\LikeStory;
use App\Models\Story;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StoryService
{
    /**
     * Lấy danh sách tin 24h còn hạn sử dụng.
     *
     * @param  int  $currentUserId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveStories(int $currentUserId)
    {
        return Story::with('user')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->latest()
            ->get();
    }

    /**
     * Đăng tin 24h mới.
     *
     * @param  int    $userId
     * @param  mixed  $mediaFile
     * @param  string|null  $content
     * @return \App\Models\Story
     */
    public function createStory(int $userId, $mediaFile, ?string $content = null): Story
    {
        $path = $mediaFile->store('stories', 'public');
        $mediaUrl = Storage::url($path);

        $mimeType = $mediaFile->getMimeType();
        $mediaType = str_starts_with($mimeType, 'video') ? 'video' : 'image';

        return Story::create([
            'user_id'    => $userId,
            'content'    => $content,
            'media_url'  => $mediaUrl,
            'media_type' => $mediaType,
            'expires_at' => Carbon::now()->addHours(24),
        ])->load('user');
    }

    /**
     * Xóa Story 24h.
     *
     * @param  int  $storyId
     * @param  int  $userId
     * @return bool
     */
    public function deleteStory(int $storyId, int $userId): bool
    {
        $story = Story::where('id', $storyId)->where('user_id', $userId)->first();
        if (!$story) {
            return false;
        }

        $story->delete();
        return true;
    }

    /**
     * Toggle Like Story 24h.
     *
     * @param  int  $storyId
     * @param  int  $userId
     * @return array
     */
    public function toggleLike(int $storyId, int $userId): array
    {
        $like = LikeStory::where('story_id', $storyId)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            return ['liked' => false];
        }

        LikeStory::create([
            'story_id' => $storyId,
            'user_id'  => $userId,
        ]);
        return ['liked' => true];
    }
}
