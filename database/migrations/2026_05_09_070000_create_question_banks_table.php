<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('option_a', 255);
            $table->string('option_b', 255);
            $table->string('option_c', 255)->nullable();
            $table->string('option_d', 255)->nullable();
            $table->string('correct_option', 1);
            $table->unsignedSmallInteger('marks')->default(1);
            $table->timestamps();

            $table->index(['teacher_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
