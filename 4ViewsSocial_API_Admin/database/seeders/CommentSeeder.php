<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('comments')->insert([
            [
                'user_id' => 1,
                'post_id' => 1,
                'comment' => 'Nice post!',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
