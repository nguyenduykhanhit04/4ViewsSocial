<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('stories')->insert([
            [
                'user_id' => 1,
                'video_url' => 'story1.mp4',
                'expired_time' => now()->addHours(24),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
