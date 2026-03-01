<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * SchoolClass Model — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * Represents a school class (e.g. "Grade 10 - Section A").
 * Named SchoolClass to avoid collision with PHP's reserved keyword 'Class'.
 *
 * OOP: Composition — a SchoolClass has many Subjects.
 *
 * @property int    $id
 * @property string $name
 * @property string|null $section
 */
class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'section'];

    /**
     * A class has many subjects.
     * OOP: Composition
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    /**
     * A class has many student detail records.
     */
    public function studentDetails(): HasMany
    {
        return $this->hasMany(StudentDetail::class, 'class_id');
    }

    /**
     * Human-readable display name: "Grade 10 — A"
     */
    public function getFullNameAttribute(): string
    {
        return $this->section
            ? $this->name . ' — ' . $this->section
            : $this->name;
    }
}
