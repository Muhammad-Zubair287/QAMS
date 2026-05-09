<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->dateTime('deadline_at');
            $table->timestamps();

            $table->index(['teacher_id', 'subject_id']);
            $table->index('deadline_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
