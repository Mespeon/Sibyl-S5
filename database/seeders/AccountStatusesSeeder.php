<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

class AccountStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('account_statuses')->delete();
        DB::table('account_statuses')->insert([
            [
                'id' => 1,
                'key' => 'active',
                'name' => 'Active',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 2,
                'key' => 'inactive',
                'name' => 'Inactive',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ],
            [
                'id' => 3,
                'key' => 'deactivated',
                'name' => 'Deactivated',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
