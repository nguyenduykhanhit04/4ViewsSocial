<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\AdminUserResource;
use App\Services\AdminUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class UserController extends Controller
{
    protected AdminUserService $userService;

    public function __construct(AdminUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Lấy danh sách người dùng cho màn hình Admin.
     */
    public function listUsers(Request $request): JsonResponse
    {
        try {
            $search = $request->input('search');
            $users = $this->userService->getUsers($search);

            return ApiResponse::success(AdminUserResource::collection($users));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Lấy chi tiết thông tin một người dùng.
     */
    public function getUserById(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            if (!$user) {
                return ApiResponse::notFound('Không tìm thấy người dùng.');
            }

            return ApiResponse::success(new AdminUserResource($user));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy thông tin người dùng: ' . $e->getMessage());
        }
    }

    /**
     * Khóa hoặc Mở khóa tài khoản người dùng.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $result = $this->userService->toggleUserStatus($id);
            if (!$result['success']) {
                return ApiResponse::badRequest($result['message']);
            }

            return ApiResponse::success($result, $result['message']);
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi thay đổi trạng thái tài khoản: ' . $e->getMessage());
        }
    }
}
