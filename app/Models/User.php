<?php

namespace App\Models;

use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model — QAMS
 * ─────────────────────────────────────────────────────────────────────────
 * OOP Concept: Inheritance
 *   → This class inherits from Laravel's Authenticatable base class,
 *     which gives us login/auth functionality for free.
 *
 * Represents all system users: Admin, Teacher, and Student.
 *
 * @property int    $id
 * @property string $name
 * @property string $user_name
 * @property string $password
 * @property string $role        (admin | teacher | student)
 * @property string $active      (yes | no)
 */
class User extends Authenticatable
{
    use Notifiable;

    // Tell Laravel which table to use
    protected $table = 'users';

    /**
     * Mass-assignable columns.
     * Only these fields can be filled using User::create([...])
     */
    protected $fillable = [
        'name',
        'user_name',
        'password',
        'role',
        'active',
    ];

    /**
     * Hidden fields — never returned in JSON responses.
     * Keeps passwords safe.
     */
    protected $hidden = [
        'password',
    ];

    // ── OOP: Polymorphism — role behaviour methods ────────────────────────

    /**
     * Is this user an Admin?
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Is this user a Teacher?
     * @return bool
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Is this user a Student?
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Is this account active (not blocked)?
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active === 'yes';
    }

    /**
     * is_blocked accessor — used in Blade views.
     * Returns true when active === 'no'.
     * @return bool
     */
    public function getIsBlockedAttribute(): bool
    {
        return $this->active === 'no';
    }

    /**
     * Disable "remember me" token — our table has no remember_token column.
     */
    public function getRememberTokenName(): ?string
    {
        return null;
    }

    // ── OOP: Association / Composition — relationships ────────────────

    /**
     * Get the student detail record for this user.
     * OOP: Association (one-to-one)
     */
    public function studentDetail(): HasOne
    {
        return $this->hasOne(StudentDetail::class, 'user_id');
    }

    /**
     * Get the teacher detail record for this user.
     */
    public function teacherDetail(): HasOne
    {
        return $this->hasOne(TeacherDetail::class, 'user_id');
    }

    /**
     * Subjects assigned to this teacher.
     * OOP: Aggregation (many-to-many via teacher_subjects pivot)
     */
    public function teachingSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id');
    }

    /**
     * Subjects this student is enrolled in.
     */
    public function enrolledSubjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id');
    }

    /**
     * Get the correct dashboard route name based on the user's role.
     * OOP: Polymorphism — same method, different result per role.
     *
     * @return string  Route name
     */
    public function getDashboardRoute(): string
    {
        // Map each role to its dashboard route
        $routes = [
            'admin'   => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            'student' => 'student.dashboard',
        ];

        // Return the route for this role, or fall back to login
        return $routes[$this->role] ?? 'login';
    }
}
