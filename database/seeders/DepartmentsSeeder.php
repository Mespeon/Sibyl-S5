<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->delete();
        DB::table('departments')->insert([
            [
                'id' => 1,
                'code' => 'DCS',
                'name' => 'Department of Computer Studies',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
