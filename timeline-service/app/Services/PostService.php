<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Favourite;
use App\Models\LikePost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Exception;

class PostService
{
    /**
     * Lấy danh sách bài viết trên Bảng tin (News Feed).
     *
     * @param  int  $userId  ID người dùng đang xem
     * @param  int  $page    Trang hiện tại
     * @param  int  $limit   Số bài mỗi trang
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFeedPosts(int $userId, int $page = 1, int $limit = 10)
    {
        return Post::with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * Tạo bài viết mới kèm tệp tải lên (ảnh/video).
     *
     * @param  int    $userId
     * @param  array  $data
     * @param  mixed  $imageFile
     * @param  mixed  $videoFile
     * @return \App\Models\Post
     */
    public function createPost(int $userId, array $data, $imageFile = null, $videoFile = null): Post
    {
        $thumbnailUrl = null;

        if ($imageFile) {
            $path = $imageFile->store('posts/images', 'public');
            $thumbnailUrl = Storage::url($path);
        }

        return Post::create([
            'user_id'       => $userId,
            'caption'       => $data['content'] ?? ($data['caption'] ?? ''),
            'thumbnail_url' => $thumbnailUrl,
            'total_like'    => 0,
            'total_comment' => 0,
        ]);
    }

    /**
     * Xóa bài viết của người dùng.
     *
     * @param  int  $postId
     * @param  int  $userId
     * @return bool
     */
    public function deletePost(int $postId, int $userId): bool
    {
        $post = Post::where('id', $postId)->where('user_id', $userId)->first();
        if (!$post) {
            return false;
        }

        $post->delete();
        return true;
    }

    /**
     * Toggle Like / Unlike bài viết.
     *
     * @param  int  $postId
     * @param  int  $userId
     * @return array
     */
    public function toggleLike(int $postId, int $userId): array
    {
        $like = LikePost::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            Post::where('id', $postId)->decrement('total_like');
            return ['liked' => false];
        }

        LikePost::create([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);
        Post::where('id', $postId)->increment('total_like');
        return ['liked' => true];
    }

    /**
     * Toggle Bookmark / Save bài viết vào mục yêu thích.
     *
     * @param  int  $postId
     * @param  int  $userId
     * @return array
     */
    public function toggleSave(int $postId, int $userId): array
    {
        $favourite = Favourite::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($favourite) {
            $favourite->delete();
            return ['saved' => false];
        }

        Favourite::create([
            'post_id' => $postId,
            'user_id' => $userId,
        ]);
        return ['saved' => true];
    }

    /**
     * Thêm bình luận mới vào bài viết.
     *
     * @param  int     $postId
     * @param  int     $userId
     * @param  string  $content
     * @return \App\Models\Comment
     */
    public function addComment(int $postId, int $userId, string $content): Comment
    {
        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        Post::where('id', $postId)->increment('total_comment');

        return $comment->load('user');
    }

    /**
     * Lấy danh sách bình luận của bài viết.
     *
     * @param  int  $postId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getComments(int $postId)
    {
        return Comment::with('user')
            ->where('post_id', $postId)
            ->latest()
            ->get();
    }
}
