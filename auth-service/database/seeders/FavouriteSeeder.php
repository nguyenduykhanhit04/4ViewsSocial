<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavouriteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('favourites')->insert([
            [
                'user_id' => 2,
                'post_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
