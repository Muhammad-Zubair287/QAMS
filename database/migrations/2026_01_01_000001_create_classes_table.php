<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * QAMS — Create Classes Table
 * Stores school classes (e.g. "Grade 10 - Section A")
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);         // e.g. "Grade 10", "Class 8"
            $table->string('section', 10)->nullable(); // e.g. "A", "B", "Science"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
