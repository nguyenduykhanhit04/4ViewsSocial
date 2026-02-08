<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

// 🔥 Firebase
use Kreait\Firebase\Factory;

class AuthController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    /**
     * =========================
     * LOGIN USERNAME / EMAIL
     * =========================
     */
    public function login(Request $request)
    {
        try {
            $user = $this->findUser($request->user_name);

            if (!$user || $user->status == User::STATUS_BANNED) {
                return $this->responseApi->BadRequest('User not found or banned');
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->responseApi->BadRequest('Password is incorrect');
            }

            // Passport / Sanctum token
            $tokenResult = $user->createToken((string) $user->id);

            $user->update([
                'token' => $tokenResult->accessToken
            ]);

            // 🔥 Firebase Custom Token
            $firebaseToken = $this->createFirebaseToken($user);

            return $this->responseApi->success([
                'access_token'   => $tokenResult->plainTextToken,
                'firebase_token' => $firebaseToken,
                'user_info'           => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return $this->responseApi->InternalServerError();
        }
    }

    /**
     * =========================
     * LOGIN WITH GOOGLE
     * =========================
     */
    public function loginWithGoogle(Request $request)
    {
        try {
            $accessToken = $request->input('access_token');

            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if ($response->failed()) {
                return $this->responseApi->BadRequest('Invalid Google token');
            }

            $googleUser = $response->json();

            $user = $this->findUser($googleUser['email']);

            if (!$user) {
                $user = User::create([
                    'full_name'     => $googleUser['name'],
                    'user_name'     => $googleUser['given_name'] . rand(100, 999),
                    'email'         => $googleUser['email'],
                    'avatar_url'    => $googleUser['picture'],
                    'role'          => User::ROLE_CLIENT,
                    'status'        => User::STATUS_ACTIVE,
                    'password'      => Hash::make(rand(100000, 999999)),
                ]);
            }

            $tokenResult = $user->createToken((string) $user->id);

            $user->update([
                'token' => $tokenResult->accessToken
            ]);

            $firebaseToken = $this->createFirebaseToken($user);

            return $this->responseApi->success([
                'access_token'   => $tokenResult->plainTextToken,
                'firebase_token' => $firebaseToken,
                'user_info'           => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('LoginWithGoogle error: ' . $e->getMessage());
            return $this->responseApi->InternalServerError();
        }
    }

    /**
     * =========================
     * REGISTER
     * =========================
     */
    public function register(Request $request)
    {
        try {
            $user = $this->findUser($request->email, $request->user_name);

            if ($user) {
                return $this->responseApi->BadRequest('User already exists');
            }

            User::create([
                'user_name' => $request->user_name,
                'full_name' => $request->full_name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => User::ROLE_CLIENT,
                'status'    => User::STATUS_ACTIVE,
            ]);

            return $this->responseApi->success();
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            return $this->responseApi->BadRequest($e->getMessage());
        }
    }

    /**
     * =========================
     * CREATE FIREBASE TOKEN
     * =========================
     */
    private function createFirebaseToken(User $user): string
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));

        $auth = $firebase->createAuth();

        // UID = user_id Laravel (STRING)
        return $auth->createCustomToken((string) $user->id)->toString();
    }

    /**
     * =========================
     * FIND USER
     * =========================
     */
    private function findUser($emailOrUsername, $username = null)
    {
        if ($username === null) {
            return User::where('email', $emailOrUsername)
                ->orWhere('user_name', $emailOrUsername)
                ->first();
        }

        return User::where('email', $emailOrUsername)
            ->orWhere('user_name', $username)
            ->first();
    }


    public function logout(Request $request)
    {
        try {
            // Revoke token hiện tại
            $userId = $request->input('user_id');
            $user = User::find($userId);
            $request->user()->token()->revoke();

            // Optional: clear token lưu DB
            $request->user()->update(['token' => null]);

            return $this->responseApi->success('Logout success');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return $this->responseApi->InternalServerError();
        }
    }
}
