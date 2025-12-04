<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TestLogin extends Command
{
    protected $signature = 'test:login {email} {password}';
    protected $description = 'Test login with email and password';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $this->info("Testing login for: {$email}");

        // Find user
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found!");
            return 1;
        }

        $this->info("User found: {$user->name}");
        $this->info("User ID: {$user->id}");

        // Test password
        if (Hash::check($password, $user->password)) {
            $this->info("✓ Password is correct!");

            // Load roles
            $user->load('roles');
            $this->info("Roles: " . $user->roles->pluck('nombre')->implode(', '));

            return 0;
        } else {
            $this->error("✗ Password is incorrect!");
            $this->info("Stored hash: " . $user->password);
            return 1;
        }
    }
}
