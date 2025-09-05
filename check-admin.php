<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(IllwareConsoleKernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Enums\RoleType;
use Illuminate\Support\Facades\Hash;

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

echo "Admin user checked/created successfully!\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . " (" . RoleType::from($user->role)->label() . ")\n";
echo "Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
