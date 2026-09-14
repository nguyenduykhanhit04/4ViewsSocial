<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Gợi ý danh sách bạn bè.
     */
    public function suggestFriend(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $users = $this->userService->getFriendSuggestions($userId);

            return ApiResponse::success(UserProfileResource::collection($users));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy gợi ý bạn bè: ' . $e->getMessage());
        }
    }

    /**
     * Theo dõi hoặc Bỏ theo dõi người dùng khác.
     */
    public function follow(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $targetUserId = (int) $request->input('following_id');

            $result = $this->userService->toggleFollow($userId, $targetUserId);
            if (!$result['success']) {
                return ApiResponse::badRequest($result['message']);
            }

            return ApiResponse::success($result, $result['message']);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi theo dõi người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Tìm kiếm người dùng theo từ khóa.
     */
    public function searchUser(Request $request): JsonResponse
    {
        try {
            $keyword = (string) $request->input('key', '');
            $users = $this->userService->searchUsers($keyword);

            return ApiResponse::success(UserProfileResource::collection($users));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi tìm kiếm người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin cơ bản của user.
     */
    public function getInfo(Request $request): JsonResponse
    {
        try {
            $userId = (int) $request->input('user_id');
            $user = User::find($userId);

            if (!$user) {
                return ApiResponse::notFound('Không tìm thấy người dùng.');
            }

            return ApiResponse::success(new UserProfileResource($user));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy thông tin người dùng: ' . $e->getMessage());
        }
    }
}
