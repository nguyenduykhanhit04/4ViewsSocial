<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Khởi tạo dữ liệu mẫu cho cơ sở dữ liệu Auth Service.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
