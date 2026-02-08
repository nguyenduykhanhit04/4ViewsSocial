<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('follows')->insert([
            [
                'user_id' => 2,
                'following_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
