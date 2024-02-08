<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courses')->delete();
        DB::table('courses')->insert([
            [
                'id' => 1,
                'code' => 'BSCS',
                'name' => 'Bachelor of Science in Computer Science',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 2,
                'code' => 'BSINFOTECH',
                'name' => 'Bachelor of Science in Information Technology',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
