<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolenceWarningSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('violence_warnings')->insert([
            [
                'user_id' => 1,
                'infringe_id' => 2
            ]
        ]);
    }
}
