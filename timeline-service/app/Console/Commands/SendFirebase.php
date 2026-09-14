<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendFirebase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-firebase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notifications = Notification::join(
            'users',
            'notifications.user_id',
            'users.id'
        )->select(
            'notifications.id',
            'notifications.title',
            'notifications.content',
            'users.device_token'
        )->where('status', Notification::STATUS_WAIT)
            ->orderBy('id', 'DESC')->get();
        if (!$notifications) {
            return null;
        }
        $firebaseService = new FirebaseService();
        foreach ($notifications as $notify) {
            $sendFCM = $firebaseService->sendFCM($notify->content, $notify->device_token);
            if ($sendFCM) {
                DB::table('notifications')->where('id', $notify->id)
                    ->update([
                        'status' => Notification::STATUS_DONE
                    ]);
                continue;
            }
            DB::table('notifications')->where('id', $notify->id)
                ->update([
                    'status' => Notification::STATUS_FAIL
                ]);
        }
    }
}
