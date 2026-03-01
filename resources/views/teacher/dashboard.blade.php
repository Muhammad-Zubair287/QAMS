{{-- Teacher Dashboard — QAMS (Placeholder for Prototype) --}}
@extends('layouts.app')
@section('title', 'Teacher Dashboard')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-person-workspace display-1 text-primary"></i>
    <h3 class="fw-bold mt-3">Welcome, {{ auth()->user()->name }}!</h3>
    <p class="text-muted">Teacher features will be built in the next phase.</p>
    <div class="mt-4">
        <span class="badge bg-info fs-6 px-4 py-2">
            <i class="bi bi-tools me-2"></i>Coming Soon: Question Bank, Quizzes, Assignments
        </span>
    </div>
</div>
@endsection
