<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AdminPostResource;
use App\Services\AdminPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class PostController extends Controller
{
    protected AdminPostService $postService;

    public function __construct(AdminPostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Lấy danh sách bài viết trên toàn hệ thống.
     */
    public function listPosts(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->input('limit', 15);
            $posts = $this->postService->getPosts($limit);

            return ApiResponse::success(AdminPostResource::collection($posts));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bài viết vi phạm bởi Admin.
     */
    public function deletePost(int $id): JsonResponse
    {
        try {
            $deleted = $this->postService->deletePost($id);
            if (!$deleted) {
                return ApiResponse::notFound('Không tìm thấy bài viết cần xóa.');
            }

            return ApiResponse::success(null, 'Đã xóa bài viết vi phạm thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi xóa bài viết: ' . $e->getMessage());
        }
    }
}
