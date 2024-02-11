<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = DB::table('users')->insertGetId([
            'username' => 'lunasa.prismriver',
            'password' => Hash::make('TouhouProject17!'),
            'status_id' => 1,
            'created_at' => Date::now(),
            'updated_at' => Date::now()
        ]);

        DB::table('user_profiles')->insert([
            [
                'user_id' => $user,
                'first_name' => 'Lunasa',
                'last_name' => 'Prismriver',
                'email_address' => 'lunasa.prismriver@mail.com',
                'contact_number' => '09051234567',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);

        DB::table('user_roles')->insert([
            [
                'user_id' => $user,
                'role_id' => 1,
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]
        ]);
    }
}
