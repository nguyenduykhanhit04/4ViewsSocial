<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CreateStoryRequest;
use App\Http\Resources\StoryResource;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class StoryController extends Controller
{
    protected StoryService $storyService;

    public function __construct(StoryService $storyService)
    {
        $this->storyService = $storyService;
    }

    /**
     * Lấy danh sách tin 24h còn hoạt động.
     */
    public function listStory(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $stories = $this->storyService->getActiveStories($userId);

            return ApiResponse::success(StoryResource::collection($stories));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách Story: ' . $e->getMessage());
        }
    }

    /**
     * Đăng tin 24h mới.
     */
    public function addStory(CreateStoryRequest $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $story = $this->storyService->createStory(
                $userId,
                $request->file('media'),
                $request->input('content')
            );

            return ApiResponse::created(new StoryResource($story), 'Đăng tin thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi đăng tin: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tin 24h.
     */
    public function deleteStory(Request $request): JsonResponse
    {
        try {
            $storyId = (int) $request->input('story_id');
            $userId = (int) ($request->input('user_id') ?? 1);

            $deleted = $this->storyService->deleteStory($storyId, $userId);
            if (!$deleted) {
                return ApiResponse::badRequest('Không tìm thấy tin hoặc bạn không có quyền xóa.');
            }

            return ApiResponse::success(null, 'Đã xóa tin thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi xóa tin: ' . $e->getMessage());
        }
    }

    /**
     * Thích hoặc Bỏ thích tin 24h.
     */
    public function likeStory(Request $request): JsonResponse
    {
        try {
            $storyId = (int) $request->input('story_id');
            $userId = (int) ($request->input('user_id') ?? 1);

            $result = $this->storyService->toggleLike($storyId, $userId);
            return ApiResponse::success($result);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi thích tin: ' . $e->getMessage());
        }
    }
}
