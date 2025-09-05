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
            // Professeur user
            [
                'name' => 'Professeur Test',
                'email' => 'professeur@example.com',
                'password' => Hash::make('password'),
                'role' => 'professeur',
                'phone' => '+212600000002',
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Assistant user
            [
                'name' => 'Assistant Test',
                'email' => 'assistant@example.com',
                'password' => Hash::make('password'),
                'role' => 'assistant',
                'phone' => '+212600000003',
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Eleve user
            [
                'name' => 'Élève Test',
                'email' => 'eleve@example.com',
                'password' => Hash::make('password'),
                'role' => 'eleve',
                'phone' => '+212600000004',
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
