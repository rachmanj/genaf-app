<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    protected $signature = 'user:verify-email {username}';

    protected $description = 'Verify user email';

    public function handle()
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("User with username '{$username}' not found.");
            return 1;
        }

        $user->email_verified_at = now();
        $user->save();

        $this->info("Email verified successfully for user '{$username}'.");
        return 0;
    }
}
