<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'user_name' => 'admin',
                'full_name' => 'Administrator',
                'gmail' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 0,
                'status' => 0,
                'online_status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_name' => 'sanpt',
                'full_name' => 'SangPT',
                'gmail' => 'phamthesang1307@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 0,
                'status' => 0,
                'online_status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_name' => 'quangjustme',
                'full_name' => 'QuangJustMe',
                'gmail' => 'nguyenkimquang1612@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 0,
                'status' => 0,
                'online_status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_name' => 'khanhpinapple',
                'full_name' => 'KhanhPinapple',
                'gmail' => 'nguyenduykhanh121204@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 0,
                'status' => 0,
                'online_status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'user_name' => 'hbdiep',
                'full_name' => 'DiepHB',
                'gmail' => 'hbdiep2004@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => 0,
                'status' => 0,
                'online_status' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
