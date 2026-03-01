<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * StudentDetail Model — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * Stores extended profile data for students.
 *
 * OOP: Association — linked to a User (one-to-one).
 * OOP: Aggregation — a student can be enrolled in many subjects.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $admission_number
 * @property string      $father_name
 * @property string|null $picture
 * @property int|null    $class_id
 */
class StudentDetail extends Model
{
    protected $table = 'student_details';

    protected $fillable = [
        'user_id',
        'admission_number',
        'father_name',
        'picture',
        'class_id',
    ];

    /**
     * A student detail belongs to one user.
     * OOP: Association
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A student detail belongs to one class.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * A student is enrolled in many subjects.
     * OOP: Aggregation (through pivot student_subjects)
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id')
                    ->withPivot('student_id');
    }
}
