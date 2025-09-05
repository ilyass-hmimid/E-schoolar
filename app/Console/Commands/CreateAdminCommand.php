<?php

namespace App\Console\Commands;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create';
    protected $description = 'Create an admin user';

    public function handle()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@allotawjih.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => RoleType::ADMIN->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->info('Admin user created/updated successfully!');
        $this->info('Email: admin@allotawjih.com');
        $this->info('Password: password');
    }
}
