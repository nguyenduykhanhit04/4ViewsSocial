<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LikeStorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('like_stories')->insert([
            [
                'user_id' => 2,
                'story_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
