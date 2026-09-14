<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserVerification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailService
{
    /**
     * Gửi email chứa mã xác thực OTP 6 ký tự khi người dùng đăng ký tài khoản.
     *
     * @param  \App\Models\User  $user  Đối tượng người dùng nhận mã xác thực
     * @return bool Trả về true nếu gửi mail thành công, false nếu xảy ra lỗi
     */
    public static function sendMailRegisterAccount(User $user): bool
    {
        try {
            $verificationCode = strtoupper(Str::random(6));

            UserVerification::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'code'       => $verificationCode,
                    'expires_at' => Carbon::now()->addMinutes(UserVerification::EXPIRED_AT),
                ]
            );

            Mail::send(
                'mails.register-account',
                [
                    'user' => $user,
                    'code' => $verificationCode,
                ],
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('🎉 Chào mừng bạn đến với 4ViewsSocial - Mã xác thực tài khoản');
                }
            );

            return true;
        } catch (Exception $e) {
            Log::error('Lỗi gửi email đăng ký tài khoản cho user ID ' . $user->id . ': ' . $e->getMessage());
            return false;
        }
    }
}
