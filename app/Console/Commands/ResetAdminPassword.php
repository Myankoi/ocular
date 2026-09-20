<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email : Admin email} {--password= : New password; prompted when omitted}';

    protected $description = 'Reset an admin password from the server shell';

    public function handle(): int
    {
        $admin = User::query()->where('email', $this->argument('email'))->where('role', 'admin')->first();

        if (! $admin) {
            $this->error('Admin dengan email tersebut tidak ditemukan.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Password baru (minimal 8 karakter)');

        if (! is_string($password) || strlen($password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        $admin->update(['password' => Hash::make($password), 'is_active' => true]);
        $this->info('Password admin berhasil direset.');

        return self::SUCCESS;
    }
}
