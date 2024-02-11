<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        $seederClasses = [
            RolesSeeder::class,
            AccountStatusesSeeder::class,
            DepartmentsSeeder::class,
            CoursesSeeder::class,
            AdminsSeeder::class
        ];
        $this->call($seederClasses);
    }
}
