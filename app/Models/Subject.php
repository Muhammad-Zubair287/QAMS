<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Subject Model — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * Represents a subject (e.g. "Mathematics") that belongs to a class.
 *
 * OOP: Association — a Subject belongs to a SchoolClass.
 * OOP: Aggregation — a Subject can have many Teachers and Students.
 *
 * @property int    $id
 * @property string $name
 * @property int    $class_id
 */
class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = ['name', 'class_id'];

    /**
     * A subject belongs to one class.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * A subject can be assigned to many teachers.
     * OOP: Aggregation (through pivot table teacher_subjects)
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'teacher_id');
    }

    /**
     * A subject can be enrolled by many students.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_subjects', 'subject_id', 'student_id');
    }
}
