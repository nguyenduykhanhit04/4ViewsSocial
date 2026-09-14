<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Lấy danh sách bài viết trên Bảng tin (News Feed).
     */
    public function listPost(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $page = (int) $request->input('page', 1);
            $posts = $this->postService->getFeedPosts($userId, $page);

            return ApiResponse::success(PostResource::collection($posts));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi lấy danh sách bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Đăng bài viết mới.
     */
    public function addPost(CreatePostRequest $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $post = $this->postService->createPost(
                $userId,
                $request->validated(),
                $request->file('image'),
                $request->file('video')
            );

            return ApiResponse::created(new PostResource($post->load('user')), 'Đăng bài viết thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi đăng bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bài viết.
     */
    public function deletePost(Request $request): JsonResponse
    {
        try {
            $postId = (int) $request->input('post_id');
            $userId = (int) ($request->input('user_id') ?? 1);

            $deleted = $this->postService->deletePost($postId, $userId);
            if (!$deleted) {
                return ApiResponse::badRequest('Không tìm thấy bài viết hoặc bạn không có quyền xóa.');
            }

            return ApiResponse::success(null, 'Đã xóa bài viết thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi xóa bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Thích hoặc Bỏ thích bài viết (Toggle Like).
     */
    public function likePost(Request $request): JsonResponse
    {
        try {
            $postId = (int) $request->input('post_id');
            $userId = (int) ($request->input('user_id') ?? 1);

            $result = $this->postService->toggleLike($postId, $userId);
            return ApiResponse::success($result);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi thích bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Lưu bài viết hoặc Bỏ lưu (Toggle Bookmark).
     */
    public function savePost(Request $request): JsonResponse
    {
        try {
            $postId = (int) $request->input('post_id');
            $userId = (int) ($request->input('user_id') ?? 1);

            $result = $this->postService->toggleSave($postId, $userId);
            return ApiResponse::success($result);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lưu bài viết: ' . $e->getMessage());
        }
    }

    /**
     * Thêm bình luận vào bài viết.
     */
    public function comment(CommentRequest $request): JsonResponse
    {
        try {
            $postId = (int) $request->input('post_id');
            $userId = (int) ($request->input('user_id') ?? 1);
            $content = $request->input('content');

            $comment = $this->postService->addComment($postId, $userId, $content);
            return ApiResponse::created(new CommentResource($comment), 'Bình luận thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi bình luận: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách bình luận của bài viết.
     */
    public function listComment(Request $request): JsonResponse
    {
        try {
            $postId = (int) $request->input('post_id');
            $comments = $this->postService->getComments($postId);

            return ApiResponse::success(CommentResource::collection($comments));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy bình luận: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách bài viết khám phá (Explore).
     */
    public function explorePost(Request $request): JsonResponse
    {
        return $this->listPost($request);
    }
}
