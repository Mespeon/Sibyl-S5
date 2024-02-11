<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class SchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('schools')->delete();
        DB::table('schools')->insert([
            [
                'code' => 'CVSU-CCAT',
                'name' => 'Cavite State University - CCAT Campus',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'code' => 'CVSU-GenTri',
                'name' => 'Cavite State University - General Trias Campus',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'code' => 'CVSU-CC',
                'name' => 'Cavite State University - Cavite City Campus',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
