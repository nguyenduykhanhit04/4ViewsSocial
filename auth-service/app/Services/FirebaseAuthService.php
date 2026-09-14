<?php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Exception;

class FirebaseAuthService
{
    /**
     * Sinh Firebase Custom Token cho User để đồng bộ realtime chat/presence.
     *
     * @param User $user
     * @return string|null
     */
    public function createCustomToken(User $user): ?string
    {
        try {
            $credentials = config('firebase.credentials');
            if (empty($credentials) || !file_exists($credentials)) {
                // Nếu chưa cấu hình file credentials thì log warning và trả về string rỗng
                Log::info('Firebase credentials not configured or file missing.');
                return null;
            }

            $firebase = (new Factory)->withServiceAccount($credentials);
            $auth = $firebase->createAuth();

            return $auth->createCustomToken((string) $user->id)->toString();
        } catch (Exception $e) {
            Log::warning('FirebaseAuthService Error: ' . $e->getMessage());
            return null;
        }
    }
}
