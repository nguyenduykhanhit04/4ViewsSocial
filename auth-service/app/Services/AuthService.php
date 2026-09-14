<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    protected GoogleAuthService $googleAuthService;
    protected FirebaseAuthService $firebaseAuthService;

    public function __construct(
        GoogleAuthService $googleAuthService,
        FirebaseAuthService $firebaseAuthService
    ) {
        $this->googleAuthService = $googleAuthService;
        $this->firebaseAuthService = $firebaseAuthService;
    }

    /**
     * Tìm user theo email hoặc username.
     */
    public function findByCredentials(string $identifier): ?User
    {
        return User::where('email', $identifier)
            ->orWhere('user_name', $identifier)
            ->first();
    }

    /**
     * Xử lý logic đăng nhập bằng username/email & password.
     */
    public function login(string $identifier, string $password): array
    {
        $user = $this->findByCredentials($identifier);

        if (!$user) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Tài khoản không tồn tại trên hệ thống.',
            ];
        }

        if ($user->status === User::STATUS_BANNED) {
            return [
                'success' => false,
                'code'    => 403,
                'message' => 'Tài khoản của bạn đã bị khóa.',
            ];
        }

        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Mật khẩu không chính xác.',
            ];
        }

        // Tạo token đăng nhập
        $tokenResult = $user->createToken((string) $user->id);
        $user->update(['token' => $tokenResult->accessToken]);

        // Tạo Firebase Token
        $firebaseToken = $this->firebaseAuthService->createCustomToken($user);

        return [
            'success' => true,
            'data'    => [
                'access_token'   => $tokenResult->plainTextToken,
                'firebase_token' => $firebaseToken,
                'user_info'      => $user,
            ],
        ];
    }

    /**
     * Xử lý logic đăng ký tài khoản mới.
     */
    public function register(array $data): array
    {
        $user = User::create([
            'user_name'     => $data['user_name'],
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'role'          => User::ROLE_CLIENT,
            'status'        => User::STATUS_ACTIVE,
            'online_status' => User::OFFLINE_STATUS,
        ]);

        return [
            'success' => true,
            'data'    => $user,
        ];
    }

    /**
     * Xử lý đăng nhập bằng Google OAuth.
     */
    public function loginWithGoogle(string $googleAccessToken): array
    {
        $googleUser = $this->googleAuthService->getUserInfo($googleAccessToken);

        if (!$googleUser || empty($googleUser['email'])) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Không thể xác thực tài khoản Google. Token không hợp lệ.',
            ];
        }

        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            // Tạo tài khoản mới từ thông tin Google
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $googleUser['given_name'] ?? 'user'));
            $userName = $baseUsername . rand(100, 999);

            $user = User::create([
                'full_name'     => $googleUser['name'] ?? $userName,
                'user_name'     => $userName,
                'email'         => $googleUser['email'],
                'avatar_url'    => $googleUser['picture'] ?? null,
                'role'          => User::ROLE_CLIENT,
                'status'        => User::STATUS_ACTIVE,
                'online_status' => User::ONLINE_STATUS,
                'password'      => Hash::make(bin2hex(random_bytes(16))),
            ]);
        }

        if ($user->status === User::STATUS_BANNED) {
            return [
                'success' => false,
                'code'    => 403,
                'message' => 'Tài khoản của bạn đã bị khóa.',
            ];
        }

        $tokenResult = $user->createToken((string) $user->id);
        $user->update(['token' => $tokenResult->accessToken]);
        $firebaseToken = $this->firebaseAuthService->createCustomToken($user);

        return [
            'success' => true,
            'data'    => [
                'access_token'   => $tokenResult->plainTextToken,
                'firebase_token' => $firebaseToken,
                'user_info'      => $user,
            ],
        ];
    }

    /**
     * Xử lý đăng xuất.
     */
    public function logout($user): bool
    {
        try {
            if ($user && method_exists($user, 'currentAccessToken')) {
                $user->currentAccessToken()?->delete();
            }
            if ($user) {
                $user->update(['token' => null]);
            }
            return true;
        } catch (Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return false;
        }
    }
}
