<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AdminStoryResource;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class StoryController extends Controller
{
    /**
     * Lấy danh sách tin 24h trên toàn hệ thống.
     */
    public function listStories(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->input('limit', 15);
            $stories = Story::with('user')->latest()->paginate($limit);

            return ApiResponse::success(AdminStoryResource::collection($stories));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách Story: ' . $e->getMessage());
        }
    }

    /**
     * Xóa tin 24h vi phạm bởi Admin.
     */
    public function deleteStory(int $id): JsonResponse
    {
        try {
            $story = Story::find($id);
            if (!$story) {
                return ApiResponse::notFound('Không tìm thấy tin cần xóa.');
            }

            $story->delete();
            return ApiResponse::success(null, 'Đã xóa tin 24h thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi xóa tin: ' . $e->getMessage());
        }
    }
}
