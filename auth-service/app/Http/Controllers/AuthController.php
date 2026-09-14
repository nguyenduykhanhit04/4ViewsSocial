<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\GoogleLoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Đăng nhập bằng email/username và mật khẩu.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->input('user_name'),
                $request->input('password')
            );

            if (!$result['success']) {
                return ApiResponse::badRequest($result['message']);
            }

            return ApiResponse::success([
                'access_token'   => $result['data']['access_token'],
                'firebase_token' => $result['data']['firebase_token'],
                'user_info'      => new UserResource($result['data']['user_info']),
            ], 'Đăng nhập thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi hệ thống khi đăng nhập: ' . $e->getMessage());
        }
    }

    /**
     * Đăng ký tài khoản người dùng mới.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return ApiResponse::created(
                new UserResource($result['data']),
                'Đăng ký tài khoản thành công.'
            );
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi hệ thống khi đăng ký: ' . $e->getMessage());
        }
    }

    /**
     * Đăng nhập qua Google OAuth.
     */
    public function loginWithGoogle(GoogleLoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->loginWithGoogle($request->input('access_token'));

            if (!$result['success']) {
                return ApiResponse::badRequest($result['message']);
            }

            return ApiResponse::success([
                'access_token'   => $result['data']['access_token'],
                'firebase_token' => $result['data']['firebase_token'],
                'user_info'      => new UserResource($result['data']['user_info']),
            ], 'Đăng nhập Google thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi hệ thống khi đăng nhập Google: ' . $e->getMessage());
        }
    }

    /**
     * Lấy thông tin user hiện tại đang đăng nhập.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::unauthorized('Chưa đăng nhập.');
        }

        return ApiResponse::success(new UserResource($user));
    }

    /**
     * Đăng xuất & thu hồi token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authService->logout($user);

        return ApiResponse::success(null, 'Đăng xuất thành công.');
    }
}
