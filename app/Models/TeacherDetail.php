<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * TeacherDetail Model — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * Stores extended profile data for teachers.
 *
 * OOP: Association — linked to a User (one-to-one).
 * OOP: Aggregation — a teacher can be assigned many subjects.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string|null $job_history
 * @property string|null $education
 * @property string|null $picture
 */
class TeacherDetail extends Model
{
    protected $table = 'teacher_details';

    protected $fillable = [
        'user_id',
        'job_history',
        'education',
        'picture',
    ];

    /**
     * A teacher detail belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A teacher can be assigned many subjects.
     * OOP: Aggregation (through pivot teacher_subjects)
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id');
    }
}
