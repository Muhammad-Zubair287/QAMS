@extends('layouts.student')
@section('title', 'Student Dashboard')

@section('content')
<div class="card qams-card mb-4">
    <div class="card-body p-4">
        <h4 class="page-title mb-2"><i class="bi bi-mortarboard me-2"></i>Welcome, {{ $student->name }}</h4>
        <p class="text-muted mb-0">Access quizzes, submit assignments before deadlines, and view published results.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-6 col-lg-4">
        <div class="card qams-card"><div class="card-body"><div class="small text-muted">Enrolled Subjects</div><div class="h4 fw-bold text-primary mb-0">{{ $summary['enrolled_subjects'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card qams-card"><div class="card-body"><div class="small text-muted">Available Quizzes</div><div class="h4 fw-bold text-success mb-0">{{ $summary['available_quizzes'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card qams-card"><div class="card-body"><div class="small text-muted">Pending Assignments</div><div class="h4 fw-bold text-warning mb-0">{{ $summary['pending_assignments'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card qams-card"><div class="card-body"><div class="small text-muted">Attempted Quizzes</div><div class="h4 fw-bold text-info mb-0">{{ $summary['attempted_quizzes'] }}</div></div></div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card qams-card"><div class="card-body"><div class="small text-muted">Submitted Assignments</div><div class="h4 fw-bold" style="color:#7c3aed">{{ $summary['submitted_assignments'] }}</div></div></div>
    </div>
</div>
@endsection
