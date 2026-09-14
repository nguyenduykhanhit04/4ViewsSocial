<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAuthService
{
    /**
     * Xác thực và lấy thông tin user từ Google access token.
     *
     * @param string $accessToken
     * @return array|null
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

            Log::warning('Google OAuth verification failed: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('GoogleAuthService Error: ' . $e->getMessage());
            return null;
        }
    }
}
