<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAuthService
{
    /**
     * Xác thực Access Token với máy chủ Google OAuth2 và lấy thông tin tài khoản người dùng.
     *
     * @param  string  $accessToken  Access Token nhận được từ Google Sign-In SDK phía client
     * @return array|null  Trả về mảng thông tin user (email, name, picture...) nếu hợp lệ, ngược lại trả về null
     */
    public function getUserInfo(string $accessToken): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Xác thực Google OAuth thất bại: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('Lỗi ngoại lệ trong GoogleAuthService: ' . $e->getMessage());
            return null;
        }
    }
}
