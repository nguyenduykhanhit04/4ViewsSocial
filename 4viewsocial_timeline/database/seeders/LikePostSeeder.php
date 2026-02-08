<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LikePostSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('like_posts')->insert([
            [
                'user_id' => 2,
                'post_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
