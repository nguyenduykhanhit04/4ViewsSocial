<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Lấy dữ liệu trang cá nhân chi tiết.
     */
    public function getProfile(Request $request): JsonResponse
    {
        try {
            $userId = (int) $request->input('user_id');
            $currentUserId = (int) ($request->input('current_user_id') ?? $userId);

            $profileData = $this->profileService->getProfile($userId, $currentUserId);
            if (!$profileData) {
                return ApiResponse::notFound('Không tìm thấy người dùng.');
            }

            return ApiResponse::success([
                'user'            => new UserProfileResource($profileData['user']),
                'total_posts'     => $profileData['total_posts'],
                'total_followers' => $profileData['total_followers'],
                'total_following' => $profileData['total_following'],
                'is_following'    => $profileData['is_following'],
            ]);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy thông tin trang cá nhân: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách bài viết của một user.
     */
    public function getPosts(Request $request): JsonResponse
    {
        try {
            $userId = (int) $request->input('user_id');
            $posts = \App\Models\Post::with(['user', 'comments.user'])
                ->where('user_id', $userId)
                ->latest()
                ->get();

            return ApiResponse::success(PostResource::collection($posts));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy bài viết người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách bài viết đã lưu.
     */
    public function getPostSaved(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $posts = $this->profileService->getSavedPosts($userId);

            return ApiResponse::success(PostResource::collection($posts));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy bài viết đã lưu: ' . $e->getMessage());
        }
    }

    /**
     * Cập nhật thông tin trang cá nhân.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $user = User::find($userId);

            if (!$user) {
                return ApiResponse::notFound('Không tìm thấy người dùng.');
            }

            $updatedUser = $this->profileService->updateProfile(
                $user,
                $request->validated(),
                $request->file('avatar')
            );

            return ApiResponse::success(new UserProfileResource($updatedUser), 'Cập nhật trang cá nhân thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật thông tin: ' . $e->getMessage());
        }
    }

    /**
     * Đổi mật khẩu.
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $user = User::find($userId);

            if (!$user) {
                return ApiResponse::notFound('Không tìm thấy người dùng.');
            }

            $result = $this->profileService->changePassword(
                $user,
                $request->input('current_password'),
                $request->input('new_password')
            );

            if (!$result['success']) {
                return ApiResponse::badRequest($result['message']);
            }

            return ApiResponse::success(null, $result['message']);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi đổi mật khẩu: ' . $e->getMessage());
        }
    }
}
