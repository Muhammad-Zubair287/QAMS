<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS — Create Student Details Table
 * Stores extended profile information for students.
 * OOP Concept: Association — a StudentDetail belongs to a User.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')            // FK → users.id (must be role=student)
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('admission_number', 50)->unique(); // Unique admission/roll number
            $table->string('father_name', 60);
            $table->string('picture', 255)->nullable();       // File path to uploaded photo
            $table->foreignId('class_id')                     // FK → classes.id
                  ->nullable()
                  ->constrained('classes')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
