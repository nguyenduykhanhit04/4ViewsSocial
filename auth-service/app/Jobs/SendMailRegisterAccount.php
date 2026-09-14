<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailRegisterAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Đối tượng người dùng nhận email.
     *
     * @var \App\Models\User|null
     */
    protected ?User $user;

    /**
     * Khởi tạo một hàng đợi gửi mail đăng ký tài khoản.
     *
     * @param  int|string  $userId  ID của người dùng
     * @return void
     */
    public function __construct($userId)
    {
        $this->user = User::find($userId);
    }

    /**
     * Thực thi tác vụ gửi email trong hàng đợi.
     *
     * @return void
     */
    public function handle(): void
    {
        if ($this->user) {
            MailService::sendMailRegisterAccount($this->user);
        }
    }
}
