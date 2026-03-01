<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * Creates a default admin account so you can log in immediately
 * after running: php artisan migrate --seed
 *
 * Credentials:
 *   Username : admin
 *   Password : admin123
 *
 * firstOrCreate() is idempotent — safe to run multiple times,
 * it won't create duplicates.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            // Search condition: find by username
            ['user_name' => 'admin'],
            // If not found, create with these values
            [
                'name'     => 'System Admin',
                'password' => Hash::make('admin123'), // Hash the password!
                'role'     => 'admin',
                'active'   => 'yes',
            ]
        );

        $this->command->info('✅ Default admin created — Username: admin | Password: admin123');
    }
}
