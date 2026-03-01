<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS — Student–Subject Pivot Table
 * Many-to-many: a student can be enrolled in many subjects.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');
            $table->primary(['student_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};
