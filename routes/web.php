<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|──────────────────────────────────────────────────────────────────────────────
| QAMS Web Routes
|──────────────────────────────────────────────────────────────────────────────
| Route structure:
|   Guest routes    → /login, /register (only accessible when NOT logged in)
|   Auth routes     → /logout           (only when logged in)
|   Admin routes    → /admin/*          (only for admin role)
|   Teacher routes  → /teacher/*        (only for teacher role)
|   Student routes  → /student/*        (only for student role)
*/

// ── Root: redirect to dashboard if logged in, otherwise to login ──────────────
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return redirect()->route($user->getDashboardRoute());
    }
    return redirect()->route('login');
});

// ── Guest Routes (must NOT be logged in) ─────────────────────────────────────
Route::middleware('guest')->group(function () {

    // Registration
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ── Authenticated Routes (must be logged in) ──────────────────────────────────
Route::middleware('auth')->group(function () {

    // Logout (POST to prevent CSRF attacks)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Admin Routes (only role=admin) ───────────────────────────────────────
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard — list all users (with optional ?search=, ?role=, ?status= filters)
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Create user form (admin-only)
            Route::get('/users/create', [DashboardController::class, 'createUser'])->name('users.create');

            // Store newly created user
            Route::post('/users',        [DashboardController::class, 'storeUser'])->name('users.store');

            // Edit user form
            Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('users.edit');

            // Update user (PUT method — HTML forms use @method('PUT'))
            Route::put('/users/{id}',    [DashboardController::class, 'updateUser'])->name('users.update');

            // Block / Unblock (PATCH method)
            Route::patch('/users/{id}/toggle-block', [DashboardController::class, 'toggleBlock'])->name('users.toggle-block');

            // Delete user permanently
            Route::delete('/users/{id}', [DashboardController::class, 'destroyUser'])->name('users.destroy');

            // ── Classes ────────────────────────────────────────────────────
            Route::get('/classes',              [ClassController::class, 'index'])->name('classes.index');
            Route::get('/classes/create',       [ClassController::class, 'create'])->name('classes.create');
            Route::post('/classes',             [ClassController::class, 'store'])->name('classes.store');
            Route::get('/classes/{id}/edit',    [ClassController::class, 'edit'])->name('classes.edit');
            Route::put('/classes/{id}',         [ClassController::class, 'update'])->name('classes.update');
            Route::delete('/classes/{id}',      [ClassController::class, 'destroy'])->name('classes.destroy');

            // ── Subjects ───────────────────────────────────────────────────
            Route::get('/subjects',             [SubjectController::class, 'index'])->name('subjects.index');
            Route::get('/subjects/create',      [SubjectController::class, 'create'])->name('subjects.create');
            Route::post('/subjects',            [SubjectController::class, 'store'])->name('subjects.store');
            Route::get('/subjects/{id}/edit',   [SubjectController::class, 'edit'])->name('subjects.edit');
            Route::put('/subjects/{id}',        [SubjectController::class, 'update'])->name('subjects.update');
            Route::delete('/subjects/{id}',     [SubjectController::class, 'destroy'])->name('subjects.destroy');

            // ── Students ───────────────────────────────────────────────────
            Route::get('/students',             [StudentController::class, 'index'])->name('students.index');
            Route::get('/students/create',      [StudentController::class, 'create'])->name('students.create');
            Route::post('/students',            [StudentController::class, 'store'])->name('students.store');
            Route::get('/students/{id}/edit',   [StudentController::class, 'edit'])->name('students.edit');
            Route::put('/students/{id}',        [StudentController::class, 'update'])->name('students.update');
            Route::delete('/students/{id}',     [StudentController::class, 'destroy'])->name('students.destroy');

            // ── Teachers ───────────────────────────────────────────────────
            Route::get('/teachers',             [TeacherController::class, 'index'])->name('teachers.index');
            Route::get('/teachers/create',      [TeacherController::class, 'create'])->name('teachers.create');
            Route::post('/teachers',            [TeacherController::class, 'store'])->name('teachers.store');
            Route::get('/teachers/{id}/edit',   [TeacherController::class, 'edit'])->name('teachers.edit');
            Route::put('/teachers/{id}',        [TeacherController::class, 'update'])->name('teachers.update');
            Route::delete('/teachers/{id}',     [TeacherController::class, 'destroy'])->name('teachers.destroy');

            // ── Reports ────────────────────────────────────────────────────
            Route::get('/reports',              [ReportController::class, 'index'])->name('reports.index');
        });

    // ── Teacher Dashboard (placeholder for now) ───────────────────────────────
    Route::middleware('role:teacher')
        ->get('/teacher/dashboard', fn () => view('teacher.dashboard'))
        ->name('teacher.dashboard');

    // ── Student Dashboard (placeholder for now) ───────────────────────────────
    Route::middleware('role:student')
        ->get('/student/dashboard', fn () => view('student.dashboard'))
        ->name('student.dashboard');
});
