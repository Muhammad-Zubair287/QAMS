<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\View\View;

/**
 * ReportController — QAMS Admin
 * ─────────────────────────────────────────────────────────────────────────
 * OOP: Encapsulation — report generation logic grouped in one class.
 * OOP: Multi-dimensional arrays — data compiled as nested arrays for views.
 *
 * Generates:
 *   → Student report (list, class breakdown)
 *   → Teacher report (list, subject assignments)
 *   → Subject/Class report (coverage stats)
 */
class ReportController extends Controller
{
    /** GET /admin/reports */
    public function index(): View
    {
        // ── Report: All Students ──────────────────────────────────────────
        $students = User::where('role', 'student')
            ->with(['studentDetail.schoolClass', 'enrolledSubjects'])
            ->orderBy('name')
            ->get();

        // ── Report: All Teachers ──────────────────────────────────────────
        $teachers = User::where('role', 'teacher')
            ->with(['teacherDetail', 'teachingSubjects.schoolClass'])
            ->orderBy('name')
            ->get();

        // ── Report: All Classes with subjects + student counts ────────────
        $classes = SchoolClass::with(['subjects.teachers', 'studentDetails'])
            ->withCount('subjects')
            ->orderBy('name')
            ->get();

        // ── Report Summary (multi-dimensional array) ──────────────────────
        /*
         * Multi-dimensional array concept:
         * $summary is a 2D array — each element is a named stats group.
         */
        $summary = [
            'total_students'        => $students->count(),
            'active_students'       => $students->where('active', 'yes')->count(),
            'blocked_students'      => $students->where('active', 'no')->count(),
            'total_teachers'        => $teachers->count(),
            'active_teachers'       => $teachers->where('active', 'yes')->count(),
            'blocked_teachers'      => $teachers->where('active', 'no')->count(),
            'total_classes'         => $classes->count(),
            'total_subjects'        => Subject::count(),
            // Keys expected by reports/index.blade.php
            'blocked_users'         => $students->where('active', 'no')->count()
                                     + $teachers->where('active', 'no')->count(),
            'students_with_class'   => $students->filter(fn ($s) => $s->studentDetail?->class_id)->count(),
            'teachers_with_subjects'=> $teachers->filter(fn ($t) => $t->teachingSubjects->isNotEmpty())->count(),
        ];

        return view('admin.reports.index', compact('students', 'teachers', 'classes', 'summary'));
    }
}
