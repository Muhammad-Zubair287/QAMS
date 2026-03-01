<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS — Create Teacher Details Table
 * Stores extended profile information for teachers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')            // FK → users.id (must be role=teacher)
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->text('job_history')->nullable(); // Previous work experience
            $table->text('education')->nullable();   // Educational qualifications
            $table->string('picture', 255)->nullable(); // Profile picture
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_details');
    }
};
