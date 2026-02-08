<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMailRegisterAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
   
    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->user = User::find($userId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        MailService::sendMailRegisterAccount($this->user);
    }
}
