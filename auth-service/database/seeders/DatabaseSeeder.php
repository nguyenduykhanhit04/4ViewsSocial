<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
            LikePostSeeder::class,
            FavouriteSeeder::class,
            FollowSeeder::class,
            NotificationSeeder::class,
            ViolenceWarningSeeder::class,
            StorySeeder::class,
            LikeStorySeeder::class,
        ]);
    }
}
