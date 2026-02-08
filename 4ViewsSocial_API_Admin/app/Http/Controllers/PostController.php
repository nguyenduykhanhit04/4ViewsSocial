<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    /**
     * List posts kèm tên người đăng
     */
    public function listPosts(Request $request)
    {
        $query = Post::select(
                'posts.id',
                'posts.user_id',
                'users.full_name as user_name',
                'posts.thumbnail_url',
                'posts.caption',
                'posts.total_like',
                'posts.total_comment',
                'posts.created_at'
            )
            ->join('users', 'users.id', '=', 'posts.user_id')
            ->whereNull('posts.deleted_at');

        // Lọc theo user_id (người đăng)
        if ($request->user_id) {
            $query->where('posts.user_id', $request->user_id);
        }

        // Lọc theo khoảng ngày
        if ($request->date_from) {
            $query->where('posts.created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('posts.created_at', '<=', $request->date_to);
        }

        // Lọc theo caption (tìm kiếm)
        if ($request->caption) {
            $query->where('posts.caption', 'like', '%' . $request->caption . '%');
        }

        $posts = $query->orderBy('posts.id', 'desc')->get();

        if ($posts->isEmpty()) {
            return $this->response->dataNotfound();
        }

        return $this->response->success($posts);
    }

    /**
     * Xóa bài đăng vi phạm (soft delete)
     */
    public function deletePost($id)
    {
        if (!is_numeric($id)) {
            return $this->response->BadRequest("ID không hợp lệ");
        }

        $post = Post::find($id);

        if (!$post) {
            return $this->response->dataNotfound();
        }

        $post->deleted_at = now();
        $post->save();

        return $this->response->success([], "Đã xóa bài đăng thành công");
    }
}
