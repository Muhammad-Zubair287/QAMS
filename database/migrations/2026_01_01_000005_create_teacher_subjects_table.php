<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS — Teacher–Subject Pivot Table
 * Many-to-many: a teacher can teach many subjects; a subject can be taught by many teachers.
 * OOP Concept: Aggregation
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade');
            $table->primary(['teacher_id', 'subject_id']); // Composite primary key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};
