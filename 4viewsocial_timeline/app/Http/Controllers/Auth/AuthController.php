<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseApi;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailRegisterAccount;
use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Google\Cloud\Storage\Connection\Rest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $responseApi;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
    }

    /**
     * Login user
     * 
     * @bodyParam email string required The email of user. 
     * @bodyParam password string required The password of user.
     */
    public function login(Request $request)
    {
        $param = $request->all();
        try {
            $user = $this->findUser($request->email);
            if (!$user || $user->status == User::STATUS_BANNED) {
                return $this->responseApi->BadRequest(__('message.user_not_found_or_banned'));
            }

            if (!Hash::check($param['password'], $user->password)) {
                return $this->responseApi->BadRequest(__('message.password_incorrect'));
            }
            Auth::login($user);
            $success = $user->createToken($user->id);
            $user->update([
                'device_token' => $success->accessToken
            ]);
            $success->user_info = $user;
            return $this->responseApi->success($success);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->responseApi->BadRequest($e->getMessage());
        }
    }

    /**
     * Login user with Google account
     * 
     * @bodyParam access_token string required The access token of Google account.
     */
    public function loginWithGoogle(Request $request)
    {
        try {
            $accessToken = $request->input('access_token');
            // Gọi Google API lấy user info
            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken",
            ])->get('https://www.googleapis.com/oauth2/v3/userinfo');
            if ($response->failed()) {
                return response()->json(['error' => __('message.invalid_token')], 401);
            }
            $googleUser = $response->json();
            // check user xem có chưa
            $user = $this->findUser($googleUser['email'], $googleUser['given_name']);
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser['name'],
                    'email' => $googleUser['email'],
                    'avatar' => $googleUser['picture'],
                    'role' => User::ROLE_CLIENT,
                    'status' => User::ONLINE_STATUS,
                    'online_status' => User::ONLINE_STATUS
                ]);
            }
            Auth::login($user);
            $success = $user->createToken($user->id);
            $user->update([
                'device_token' => $success->accessToken
            ]);
            $success->user_info = $user;
            return $this->responseApi->success($success);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->responseApi->InternalServerError();
        }
    }

    /**
     * Register a new user.
     *
     * @bodyParam user_name string required User name.
     * @bodyParam full_name string required Full name.
     * @bodyParam email string required Email.
     * @bodyParam password string required Password.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $param = $request->all();
        $user = $this->findUser($param['email'], $param['user_name']);
        if ($user) {
            return $this->responseApi->BadRequest(__('message.user_already_exist'));
        }
        $user = User::create([
            'user_name' => $param['user_name'],
            'full_name' => $param['full_name'],
            'email' => $param['email'],
            'password' => Hash::make($param['password']),
            'role' => User::ROLE_CLIENT,
            'status' => User::STATUS_ACTIVE,
        ]);
        SendMailRegisterAccount::dispatch($user->id);

        return $this->responseApi->success();
    }

    /**
     * Verify account with code sent to email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @bodyParam email string required Email of user.
     * @bodyParam user_name string required Username of user.
     * @bodyParam code string required Code sent to email.
     */
    public function verifyAccount(Request $request)
    {
        $param = $request->all();
        $user = $this->findUser($param['email'], $param['user_name']);
        if (!$user || $user->status == User::STATUS_BANNED) {
            return $this->responseApi->BadRequest(__('message.user_not_found_or_banned'));
        }
        $userVerification = UserVerification::where('user_id', $user->id)->first();
        if (!$userVerification) {
            return $this->responseApi->BadRequest(__('message.user_not_found'));
        }
        if ($userVerification->expires_at < Carbon::now()) {
            return $this->responseApi->BadRequest(__('message.expired_code'));
        }
        if ($userVerification->code != $param['code']) {
            return $this->responseApi->BadRequest(__('message.invalid_code'));
        }
        $userVerification->delete();

        return $this->responseApi->success();
    }

    /**
     * Find user by email or username.
     *
     * @param string $email
     * @param string|null $username
     * @return \App\Models\User|null
     */
    private function findUser($email, $username = null)
    {
        return User::where('email', $email)->orWhere('user_name', $username)
            ->first();
    }
}
