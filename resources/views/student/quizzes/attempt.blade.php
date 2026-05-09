@extends('layouts.student')
@section('title', 'Attempt Quiz')

@section('content')
<div class="card qams-card mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold text-primary mb-1">{{ $quiz->title }}</h5>
        <p class="text-muted mb-1">Subject: {{ $quiz->subject?->name ?? '—' }}</p>
        <p class="text-muted mb-0">Deadline: {{ $quiz->deadline_at?->format('d M Y h:i A') ?? '—' }}</p>
    </div>
</div>

@if($questions->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-circle me-2"></i>This quiz has no question bank items for its subject yet.
    </div>
@else
    <form method="POST" action="{{ route('student.quizzes.submit', $quiz->id) }}">
        @csrf
        @foreach($questions as $index => $question)
            <div class="card qams-card mb-3">
                <div class="card-body">
                    <div class="fw-semibold mb-2">{{ $index + 1 }}. {{ $question->question_text }}</div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="A" id="q{{ $question->id }}a" required>
                        <label class="form-check-label" for="q{{ $question->id }}a">A. {{ $question->option_a }}</label>
                    </div>
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="B" id="q{{ $question->id }}b">
                        <label class="form-check-label" for="q{{ $question->id }}b">B. {{ $question->option_b }}</label>
                    </div>
                    @if(!empty($question->option_c))
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="C" id="q{{ $question->id }}c">
                            <label class="form-check-label" for="q{{ $question->id }}c">C. {{ $question->option_c }}</label>
                        </div>
                    @endif
                    @if(!empty($question->option_d))
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="D" id="q{{ $question->id }}d">
                            <label class="form-check-label" for="q{{ $question->id }}d">D. {{ $question->option_d }}</label>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        <button class="btn btn-primary px-4" type="submit">
            <i class="bi bi-check2-square me-1"></i>Submit Quiz
        </button>
    </form>
@endif
@endsection
