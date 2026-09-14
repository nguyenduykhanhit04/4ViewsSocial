<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\NotificationResource;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Lấy danh sách thông báo.
     */
    public function getNotifications(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $notifications = $this->notificationService->getUserNotifications($userId);

            return ApiResponse::success(NotificationResource::collection($notifications));
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lấy danh sách thông báo: ' . $e->getMessage());
        }
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $count = $this->notificationService->markAllAsRead($userId);

            return ApiResponse::success(['updated_count' => $count], 'Đã đánh dấu thông báo là đã đọc.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi cập nhật thông báo: ' . $e->getMessage());
        }
    }

    /**
     * Đăng ký Firebase Cloud Messaging device token.
     */
    public function setDeviceToken(Request $request): JsonResponse
    {
        try {
            $userId = (int) ($request->input('user_id') ?? 1);
            $deviceToken = (string) $request->input('token');

            if (empty($deviceToken)) {
                return ApiResponse::badRequest('Thiếu device token.');
            }

            $this->notificationService->updateDeviceToken($userId, $deviceToken);

            return ApiResponse::success(null, 'Đã cập nhật device token thành công.');
        } catch (Exception $e) {
            return ApiResponse::error('Lỗi khi lưu device token: ' . $e->getMessage());
        }
    }
}