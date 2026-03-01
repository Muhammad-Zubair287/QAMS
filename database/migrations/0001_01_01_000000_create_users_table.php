<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS - Create Users Table
 * ─────────────────────────────────────────────────────────────────────────
 * This migration creates the main `users` table for the QAMS system.
 * All three roles (admin, teacher, student) share this single table.
 *
 * Performance note: user_name has a UNIQUE index — fast lookup on login.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Called by: php artisan migrate
     */
    public function up(): void
    {
        // Drop existing users table variants first (fresh start for prototype)
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        // ── Create the users table ────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                 // Auto-increment primary key (INTEGER)
            $table->string('name', 30);                   // Full name — max 30 characters
            $table->string('user_name', 30)->unique();    // Unique username — indexed for fast login lookup
            $table->string('password', 255);              // Hashed password (bcrypt expands beyond 50 chars)
            $table->string('role', 15)->default('student'); // Role: admin | teacher | student
            $table->string('active', 5)->default('yes'); // Account status: yes = active, no = blocked
            $table->timestamps();                         // created_at & updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     * Called by: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
