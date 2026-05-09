@extends('layouts.teacher')
@section('title', 'Question Bank')

@section('content')
<div class="card qams-card mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold text-primary mb-0"><i class="bi bi-plus-circle me-2"></i>Add Question</h5>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="{{ route('teacher.question-bank.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->schoolClass?->full_name ?? 'No class' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Marks</label>
                    <input type="number" min="1" max="100" name="marks" class="form-control" value="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Question</label>
                    <input type="text" name="question_text" class="form-control" maxlength="2000" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Option A</label>
                    <input type="text" name="option_a" class="form-control" maxlength="255" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Option B</label>
                    <input type="text" name="option_b" class="form-control" maxlength="255" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Option C (Optional)</label>
                    <input type="text" name="option_c" class="form-control" maxlength="255">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Option D (Optional)</label>
                    <input type="text" name="option_d" class="form-control" maxlength="255">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Correct Option</label>
                    <select name="correct_option" class="form-select" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="bi bi-save me-1"></i>Save Question
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card qams-card">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold text-primary">Question Bank ({{ $questions->count() }})</h6>
    </div>
    <div class="card-body p-0">
        @if($questions->isEmpty())
            <p class="text-muted text-center py-5 mb-0">No questions added yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Subject</th>
                        <th>Question</th>
                        <th>Correct</th>
                        <th>Marks</th>
                        <th>Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($questions as $index => $question)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $question->subject?->name ?? '—' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($question->question_text, 80) }}</td>
                            <td><span class="badge bg-success">{{ $question->correct_option }}</span></td>
                            <td>{{ $question->marks }}</td>
                            <td class="small text-muted">{{ $question->created_at?->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
