@extends('layouts.app')
@section('title', 'Take Quiz: ' . $eLearningQuiz->title)

@section('content')
<div class="mb-4">
    <h4 class="mb-1">{{ $eLearningQuiz->title }}</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
        <li class="breadcrumb-item"><a href="{{ route('elearning.show', $eLearningQuiz->course) }}">{{ $eLearningQuiz->course->courseOffering->course?->name }}</a></li>
        <li class="breadcrumb-item active">Quiz</li>
    </ol></nav>
</div>

<div class="row g-4">
    <div class="col-lg-8">

        {{-- Past attempts --}}
        @if($pastAttempts->count())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-semibold mb-0">Your Previous Attempts</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Attempt</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastAttempts as $attempt)
                        <tr>
                            <td class="px-4">#{{ $attempt->attempt_number }}</td>
                            <td><strong>{{ number_format($attempt->score, 1) }}%</strong></td>
                            <td><span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">{{ $attempt->passed ? 'Passed' : 'Failed' }}</span></td>
                            <td class="text-muted small">{{ $attempt->submitted_at?->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Quiz form --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Attempt {{ $pastAttempts->count() + 1 }}</h6>
                @if($eLearningQuiz->time_limit_minutes)
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock text-warning"></i>
                    <span class="fw-semibold text-warning" id="timer">{{ sprintf('%02d:%02d', $eLearningQuiz->time_limit_minutes, 0) }}</span>
                </div>
                @endif
            </div>
            <div class="card-body px-4 pb-4">
                @if($eLearningQuiz->description)
                <div class="alert alert-light border mb-4 small">
                    <i class="bi bi-info-circle me-2 text-primary"></i>{{ $eLearningQuiz->description }}
                </div>
                @endif

                <form method="POST" action="{{ route('elearning.quizzes.submit', $eLearningQuiz) }}" id="quizForm">
                    @csrf

                    @foreach($eLearningQuiz->questions as $qi => $question)
                    <div class="mb-5">
                        <div class="d-flex gap-3 mb-3">
                            <span class="badge bg-primary rounded-pill flex-shrink-0 mt-1">{{ $qi + 1 }}</span>
                            <div>
                                <div class="fw-semibold">{{ $question->question_text }}</div>
                                <div class="text-muted small">{{ $question->marks }} mark(s)</div>
                            </div>
                        </div>
                        <div class="ms-4 ps-2">
                            @foreach($question->options as $opt)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio"
                                    name="answers[{{ $question->id }}]"
                                    value="{{ $opt->id }}"
                                    id="q{{ $question->id }}_opt{{ $opt->id }}">
                                <label class="form-check-label" for="q{{ $question->id }}_opt{{ $opt->id }}">
                                    {{ $opt->option_text }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('elearning.show', $eLearningQuiz->course) }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Submit your answers? This cannot be undone.')">
                            <i class="bi bi-send me-1"></i>Submit Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Quiz info sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm position-sticky" style="top:80px">
            <div class="card-body px-4 py-4">
                <h6 class="fw-semibold mb-3">Quiz Information</h6>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Questions</span>
                        <strong>{{ $eLearningQuiz->questions->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Marks</span>
                        <strong>{{ $eLearningQuiz->questions->sum('marks') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Pass Mark</span>
                        <strong>{{ $eLearningQuiz->passing_score }}%</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Time Limit</span>
                        <strong>{{ $eLearningQuiz->time_limit_minutes ? $eLearningQuiz->time_limit_minutes.' min' : 'None' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Attempts used</span>
                        <strong>{{ $pastAttempts->count() }} / {{ $eLearningQuiz->max_attempts }}</strong>
                    </div>
                </div>
                <hr>
                <div class="text-muted small">
                    <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
                    Answer all questions before submitting. Once submitted, answers cannot be changed.
                </div>
            </div>
        </div>
    </div>
</div>

@if($eLearningQuiz->time_limit_minutes)
@push('scripts')
<script>
(function () {
    let totalSeconds = {{ $eLearningQuiz->time_limit_minutes * 60 }};
    const timerEl = document.getElementById('timer');
    const form    = document.getElementById('quizForm');

    const interval = setInterval(function () {
        totalSeconds--;
        const m = Math.floor(totalSeconds / 60);
        const s = totalSeconds % 60;
        timerEl.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');

        if (totalSeconds <= 60) timerEl.classList.add('text-danger');
        if (totalSeconds <= 0) {
            clearInterval(interval);
            form.submit();
        }
    }, 1000);
})();
</script>
@endpush
@endif
@endsection
