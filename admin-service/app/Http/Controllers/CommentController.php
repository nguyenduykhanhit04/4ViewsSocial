<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AdminCommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CommentController extends Controller
{
    /**
     * Lấy danh sách bình luận trên toàn hệ thống cho Admin.
     */
    public function listComments(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->input('limit', 15);
            $comments = Comment::with('user')->latest()->paginate($limit);

            return ApiResponse::success(AdminCommentResource::collection($comments));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách bình luận: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bình luận vi phạm bởi Admin.
     */
    public function deleteComment(int $id): JsonResponse
    {
        try {
            $comment = Comment::find($id);
            if (!$comment) {
                return ApiResponse::notFound('Không tìm thấy bình luận cần xóa.');
            }

            $postId = $comment->post_id;
            $comment->delete();
            Post::where('id', $postId)->decrement('total_comment');

            return ApiResponse::success(null, 'Đã xóa bình luận thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi xóa bình luận: ' . $e->getMessage());
        }
    }
}
