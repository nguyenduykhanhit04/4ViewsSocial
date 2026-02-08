<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    protected $response;

    public function __construct()
    {
        $this->response = new ResponseApi();
    }

    /**
     * List comments kèm tên user và tiêu đề post
     */
    public function listComments(Request $request)
    {
        $query = Comment::select(
                'comments.id',
                'comments.user_id',
                'users.full_name as user_name',
                'comments.post_id',
                'posts.caption as post_caption',
                'comments.comment',
                'comments.created_at'
            )
            ->join('users', 'users.id', '=', 'comments.user_id')
            ->join('posts', 'posts.id', '=', 'comments.post_id');

        if ($request->user_id) {
            $query->where('comments.user_id', $request->user_id);
        }

        if ($request->post_id) {
            $query->where('comments.post_id', $request->post_id);
        }

        if ($request->date_from) {
            $query->where('comments.created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('comments.created_at', '<=', $request->date_to);
        }

        $comments = $query->orderBy('comments.id', 'desc')->get();

        if ($comments->isEmpty()) {
            return $this->response->dataNotfound();
        }

        return $this->response->success($comments);
    }

    /**
     * Xóa comment
     */
    public function deleteComment($id)
    {
        if (!is_numeric($id)) {
            return $this->response->BadRequest("ID không hợp lệ");
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return $this->response->dataNotfound();
        }

        $comment->delete();

        return $this->response->success([], "Comment đã được xóa thành công");
    }
}
