{{-- Student Dashboard — QAMS (Placeholder for Prototype) --}}
@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-person-video3 display-1 text-success"></i>
    <h3 class="fw-bold mt-3">Welcome, {{ auth()->user()->name }}!</h3>
    <p class="text-muted">Student features will be built in the next phase.</p>
    <div class="mt-4">
        <span class="badge bg-success fs-6 px-4 py-2">
            <i class="bi bi-tools me-2"></i>Coming Soon: Attempt Quizzes, Submit Assignments, View Results
        </span>
    </div>
</div>
@endsection
