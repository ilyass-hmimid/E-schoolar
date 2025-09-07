<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $users = [
            // Admin user
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+212600000001',
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert users
        foreach ($users as $user) {
            // Check if user already exists
            if (!DB::table('users')->where('email', $user['email'])->exists()) {
                DB::table('users')->insert($user);
            }
        }
    }
}
