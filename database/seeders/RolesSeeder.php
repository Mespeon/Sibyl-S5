<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class RolesSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->delete();
        DB::table('roles')->insert([
            [
                'id' => 1,
                'key' => 'admin',
                'name' => 'Administrator',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 2,
                'key' => 'user',
                'name' => 'User',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 3,
                'key' => 'student',
                'name' => 'Student',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 4,
                'key' => 'faculty',
                'name' => 'Faculty',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 5,
                'key' => 'dev',
                'name' => 'Developer',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
