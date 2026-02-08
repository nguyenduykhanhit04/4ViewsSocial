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
                'email' => 'admin@email.com',
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
                'email' => 'phamthesang1307@email.com',
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
                'email' => 'nguyenkimquang1612@email.com',
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
                'email' => 'nguyenduykhanh121204@email.com',
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
                'email' => 'hbdiep2004@email.com',
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
