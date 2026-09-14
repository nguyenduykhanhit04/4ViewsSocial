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
    /**
     * Dịch vụ xử lý nghiệp vụ xác thực.
     *
     * @var \App\Services\AuthService
     */
    protected AuthService $authService;

    /**
     * Khởi tạo AuthController.
     *
     * @param  \App\Services\AuthService  $authService
     * @return void
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Đăng nhập tài khoản bằng email hoặc tên đăng nhập và mật khẩu.
     *
     * @param  \App\Http\Requests\LoginRequest  $request  Dữ liệu đăng nhập đã qua kiểm tra hợp lệ
     * @return \Illuminate\Http\JsonResponse  Trả về token và thông tin tài khoản người dùng
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
     * Đăng ký tài khoản người dùng mới trên hệ thống.
     *
     * @param  \App\Http\Requests\RegisterRequest  $request  Dữ liệu đăng ký đã qua kiểm tra hợp lệ
     * @return \Illuminate\Http\JsonResponse  Trả về mã 201 Created và thông tin tài khoản vừa tạo
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
     * Đăng nhập hoặc tạo mới tài khoản thông qua Google OAuth2.
     *
     * @param  \App\Http\Requests\GoogleLoginRequest  $request  Chứa google access_token
     * @return \Illuminate\Http\JsonResponse  Trả về token truy cập và dữ liệu người dùng
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
     * Lấy thông tin chi tiết của người dùng đang đăng nhập (dựa trên Bearer Token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::unauthorized('Người dùng chưa đăng nhập hoặc token không hợp lệ.');
        }

        return ApiResponse::success(new UserResource($user), 'Lấy thông tin người dùng thành công.');
    }

    /**
     * Đăng xuất tài khoản và thu hồi Access Token hiện tại.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authService->logout($user);

        return ApiResponse::success(null, 'Đăng xuất tài khoản thành công.');
    }
}
