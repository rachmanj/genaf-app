<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdatePassword extends Command
{
    protected $signature = 'user:update-password {username} {password}';

    protected $description = 'Update user password';

    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("User with username '{$username}' not found.");
            return 1;
        }

        $user->password = $password;
        $user->save();

        $this->info("Password updated successfully for user '{$username}'.");
        return 0;
    }
}
