<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseAuthService
{
    /**
     * Sinh Firebase Custom Token từ thông tin User để đồng bộ phiên làm việc trên Firebase (Realtime Database / Firestore).
     *
     * @param  \App\Models\User  $user  Đối tượng người dùng cần sinh token
     * @return string|null  Chuỗi JWT Custom Token của Firebase nếu thành công, hoặc null nếu lỗi/chưa cấu hình
     */
    public function createCustomToken(User $user): ?string
    {
        try {
            $credentials = config('firebase.credentials');
            if (empty($credentials) || !file_exists($credentials)) {
                Log::info('Chưa cấu hình tệp chứng thực Firebase credentials hoặc tệp không tồn tại.');
                return null;
            }

            $firebase = (new Factory)->withServiceAccount($credentials);
            $auth = $firebase->createAuth();

            return $auth->createCustomToken((string) $user->id)->toString();
        } catch (Exception $e) {
            Log::warning('Lỗi ngoại lệ trong FirebaseAuthService: ' . $e->getMessage());
            return null;
        }
    }
}
