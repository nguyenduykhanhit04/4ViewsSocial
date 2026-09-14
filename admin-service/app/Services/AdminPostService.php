<?php

namespace App\Services;

use App\Models\Post;

class AdminPostService
{
    /**
     * Lấy danh sách bài viết có phân trang cho Admin.
     *
     * @param  int  $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPosts(int $limit = 15)
    {
        return Post::with('user')->latest()->paginate($limit);
    }

    /**
     * Xóa bài viết vi phạm.
     *
     * @param  int  $id
     * @return bool
     */
    public function deletePost(int $id): bool
    {
        $post = Post::find($id);
        if (!$post) {
            return false;
        }

        $post->delete();
        return true;
    }
}
