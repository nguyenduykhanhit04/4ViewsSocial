<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    /**
     * Dịch vụ tương tác với Google OAuth.
     *
     * @var \App\Services\GoogleAuthService
     */
    protected GoogleAuthService $googleAuthService;

    /**
     * Dịch vụ tương tác với Firebase Authentication.
     *
     * @var \App\Services\FirebaseAuthService
     */
    protected FirebaseAuthService $firebaseAuthService;

    /**
     * Khởi tạo AuthService với các dependency cần thiết.
     *
     * @param  \App\Services\GoogleAuthService  $googleAuthService
     * @param  \App\Services\FirebaseAuthService  $firebaseAuthService
     * @return void
     */
    public function __construct(
        GoogleAuthService $googleAuthService,
        FirebaseAuthService $firebaseAuthService
    ) {
        $this->googleAuthService = $googleAuthService;
        $this->firebaseAuthService = $firebaseAuthService;
    }

    /**
     * Tìm kiếm người dùng dựa trên email hoặc tên đăng nhập (user_name).
     *
     * @param  string  $identifier  Địa chỉ email hoặc tên đăng nhập
     * @return \App\Models\User|null
     */
    public function findByCredentials(string $identifier): ?User
    {
        return User::where('email', $identifier)
            ->orWhere('user_name', $identifier)
            ->first();
    }

    /**
     * Xử lý nghiệp vụ đăng nhập bằng tài khoản (username/email) và mật khẩu.
     *
     * @param  string  $identifier  Tên đăng nhập hoặc email
     * @param  string  $password    Mật khẩu của người dùng
     * @return array  Mảng kết quả chứa trạng thái, mã lỗi/dữ liệu token và thông tin người dùng
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
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ];
        }

        if (!Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Mật khẩu không chính xác.',
            ];
        }

        // Tạo API Access Token qua Laravel Sanctum
        $tokenResult = $user->createToken((string) $user->id);
        $user->update(['token' => $tokenResult->accessToken]);

        // Tạo Custom Token cho Firebase
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
     * Xử lý nghiệp vụ đăng ký tài khoản thành viên mới.
     *
     * @param  array  $data  Dữ liệu đăng ký đã qua kiểm tra hợp lệ
     * @return array  Mảng chứa trạng thái và đối tượng người dùng vừa tạo
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
     * Xử lý nghiệp vụ đăng nhập bằng tài khoản Google.
     *
     * @param  string  $googleAccessToken  Access token do Google cấp
     * @return array  Mảng chứa kết quả đăng nhập và token truy cập
     */
    public function loginWithGoogle(string $googleAccessToken): array
    {
        $googleUser = $this->googleAuthService->getUserInfo($googleAccessToken);

        if (!$googleUser || empty($googleUser['email'])) {
            return [
                'success' => false,
                'code'    => 400,
                'message' => 'Không thể xác thực tài khoản Google. Access Token không hợp lệ.',
            ];
        }

        $user = User::where('email', $googleUser['email'])->first();

        if (!$user) {
            // Tự động khởi tạo tài khoản nếu lần đầu đăng nhập Google
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
     * Xử lý đăng xuất tài khoản và vô hiệu hóa access token hiện tại.
     *
     * @param  \App\Models\User|null  $user  Người dùng đang đăng nhập
     * @return bool Trả về true nếu đăng xuất thành công
     */
    public function logout(?User $user): bool
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
            Log::error('Lỗi ngoại lệ khi đăng xuất: ' . $e->getMessage());
            return false;
        }
    }
}
