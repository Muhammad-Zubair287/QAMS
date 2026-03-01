<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Run with: php artisan db:seed
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class, // Creates the default admin account
        ]);
    }
}
