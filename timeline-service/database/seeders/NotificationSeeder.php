<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'user_id' => 1,
                'actor_id' => 2,
                'content' => 'User 2 liked your post',
                'is_view' => 0,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
