<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('posts')->insert([
            [
                'user_id' => 1,
                'caption' => 'Hello world!',
                'total_like' => 0,
                'thumbnail_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_id' => 2,
                'caption' => 'My first post!',
                'total_like' => 2,
                'thumbnail_url' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
