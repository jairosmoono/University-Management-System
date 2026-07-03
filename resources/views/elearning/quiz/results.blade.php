@extends('layouts.app')
@section('title', 'Quiz Results: ' . $eLearningQuiz->title)

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">Results: {{ $eLearningQuiz->title }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
            <li class="breadcrumb-item"><a href="{{ route('elearning.show', $eLearningQuiz->course) }}">{{ $eLearningQuiz->course->courseOffering->course?->name }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('elearning.quizzes.show', $eLearningQuiz) }}">Quiz</a></li>
            <li class="breadcrumb-item active">Results</li>
        </ol></nav>
    </div>
</div>

{{-- Summary stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-primary">{{ $attempts->count() }}</div>
            <div class="text-muted small">Students Attempted</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-success">{{ $attempts->where('passed', true)->count() }}</div>
            <div class="text-muted small">Passed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold text-danger">{{ $attempts->where('passed', false)->count() }}</div>
            <div class="text-muted small">Failed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-3 fw-bold {{ $passRate >= $eLearningQuiz->passing_score ? 'text-success' : 'text-warning' }}">{{ $passRate }}%</div>
            <div class="text-muted small">Pass Rate</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h6 class="fw-semibold mb-0">Best Score per Student</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="px-4">#</th>
                    <th>Student</th>
                    <th>Best Score</th>
                    <th>Attempts Used</th>
                    <th>Result</th>
                    <th>Last Attempt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $studentId => $attempt)
                <tr>
                    <td class="px-4 text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-semibold">{{ $attempt->student?->full_name }}</div>
                        <div class="text-muted small">{{ $attempt->student?->student_id }}</div>
                    </td>
                    <td>
                        <div class="fw-bold {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                            {{ number_format($attempt->score, 1) }}%
                        </div>
                    </td>
                    <td class="small text-muted">
                        {{ $eLearningQuiz->attempts()->where('student_id', $studentId)->count() }}
                        / {{ $eLearningQuiz->max_attempts }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                            {{ $attempt->passed ? 'Passed' : 'Failed' }}
                        </span>
                    </td>
                    <td class="text-muted small">{{ $attempt->submitted_at?->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No attempts yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
