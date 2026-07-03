@extends('layouts.app')
@section('title', $eLearningQuiz->title)

@section('content')
<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $eLearningQuiz->title }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
            <li class="breadcrumb-item"><a href="{{ route('elearning.show', $eLearningQuiz->course) }}">{{ $eLearningQuiz->course->courseOffering->course?->name }}</a></li>
            <li class="breadcrumb-item active">Quiz</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('elearning.quizzes.results', $eLearningQuiz) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-bar-chart me-1"></i>View Results
        </a>
        <span class="badge bg-{{ $eLearningQuiz->is_published ? 'success' : 'secondary' }} px-3 py-2">
            {{ $eLearningQuiz->is_published ? 'Published' : 'Draft' }}
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    {{-- Questions list --}}
    <div class="col-lg-8">

        {{-- Add question form --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-semibold mb-0">Add Question</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('elearning.questions.store', $eLearningQuiz) }}" id="questionForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Question <span class="text-danger">*</span></label>
                        <textarea name="question_text" rows="2" class="form-control form-control-sm @error('question_text') is-invalid @enderror"
                            placeholder="Enter question text…" required>{{ old('question_text') }}</textarea>
                        @error('question_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Type</label>
                            <select name="question_type" class="form-select form-select-sm" id="qtype" onchange="toggleQuestionType()">
                                <option value="single_choice">Multiple Choice (Single Answer)</option>
                                <option value="true_false">True / False</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Marks</label>
                            <input type="number" name="marks" class="form-control form-control-sm" value="{{ old('marks', 1) }}" min="1" max="10">
                        </div>
                    </div>

                    {{-- Options for multiple choice --}}
                    <div id="choiceOptions">
                        <label class="form-label small fw-semibold">Options <span class="text-muted fw-normal">(mark the correct one)</span></label>
                        @php $oldOpts = old('options', ['', '', '', '']); @endphp
                        @foreach([0,1,2,3] as $i)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct_option" value="{{ $i }}"
                                    {{ old('correct_option', 0) == $i ? 'checked' : '' }}
                                    class="form-check-input">
                            </div>
                            <input type="text" name="options[{{ $i }}]"
                                class="form-control form-control-sm"
                                placeholder="Option {{ chr(65+$i) }}"
                                value="{{ $oldOpts[$i] ?? '' }}"
                                {{ $i < 2 ? 'required' : '' }}>
                        </div>
                        @endforeach
                        <div class="text-muted small">Select the radio button next to the correct answer.</div>
                    </div>

                    {{-- True/False options (hidden by default) --}}
                    <div id="tfOptions" class="d-none">
                        <label class="form-label small fw-semibold">Correct Answer</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_option" value="0" id="tfTrue" checked>
                                <label class="form-check-label" for="tfTrue">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="correct_option" value="1" id="tfFalse">
                                <label class="form-check-label" for="tfFalse">False</label>
                            </div>
                        </div>
                        <input type="hidden" name="options[0]" value="True" disabled>
                        <input type="hidden" name="options[1]" value="False" disabled>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i>Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Existing questions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between">
                <h6 class="fw-semibold mb-0">Questions ({{ $eLearningQuiz->questions->count() }})</h6>
                <span class="text-muted small">Total: {{ $eLearningQuiz->questions->sum('marks') }} marks</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($eLearningQuiz->questions as $question)
                <div class="list-group-item px-4 py-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="d-flex gap-2">
                            <span class="badge bg-secondary rounded-pill flex-shrink-0 mt-1">{{ $loop->iteration }}</span>
                            <div>
                                <div class="fw-semibold">{{ $question->question_text }}</div>
                                <div class="text-muted small">
                                    {{ $question->question_type === 'true_false' ? 'True/False' : 'Multiple Choice' }}
                                    &bull; {{ $question->marks }} mark(s)
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('elearning.questions.destroy', $question) }}"
                            onsubmit="return confirm('Delete this question?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger flex-shrink-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <div class="ms-4 ps-2">
                        @foreach($question->options as $opt)
                        <div class="d-flex align-items-center gap-2 py-1">
                            <i class="bi bi-{{ $opt->is_correct ? 'check-circle-fill text-success' : 'circle text-muted' }}"></i>
                            <span class="small {{ $opt->is_correct ? 'fw-semibold' : '' }}">{{ $opt->option_text }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">
                    No questions yet. Add your first question above.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quiz info sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body px-4 py-4">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-primary">{{ $attemptsCount }}</div>
                        <div class="text-muted small">Attempts</div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-success">{{ $passedCount }}</div>
                        <div class="text-muted small">Passed</div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-warning">{{ $eLearningQuiz->passing_score }}%</div>
                        <div class="text-muted small">Pass Mark</div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold fs-4 text-info">{{ $avgScore ? number_format($avgScore, 1).'%' : '—' }}</div>
                        <div class="text-muted small">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body px-4 py-3">
                <div class="small mb-2"><span class="text-muted">Time limit:</span> <strong>{{ $eLearningQuiz->time_limit_minutes ? $eLearningQuiz->time_limit_minutes.' min' : 'Unlimited' }}</strong></div>
                <div class="small mb-2"><span class="text-muted">Max attempts:</span> <strong>{{ $eLearningQuiz->max_attempts }}</strong></div>
                @if($eLearningQuiz->description)
                <div class="small text-muted mt-2">{{ $eLearningQuiz->description }}</div>
                @endif
            </div>
        </div>

        @if($recentAttempts->count())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2 px-4">
                <div class="small fw-semibold">Recent Attempts</div>
            </div>
            <div class="list-group list-group-flush">
                @foreach($recentAttempts as $attempt)
                <div class="list-group-item px-4 py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small">{{ $attempt->student?->full_name }}</div>
                        <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">
                            {{ number_format($attempt->score, 1) }}%
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleQuestionType() {
    const type = document.getElementById('qtype').value;
    const choiceDiv = document.getElementById('choiceOptions');
    const tfDiv     = document.getElementById('tfOptions');

    if (type === 'true_false') {
        choiceDiv.classList.add('d-none');
        tfDiv.classList.remove('d-none');
        choiceDiv.querySelectorAll('input[type="text"]').forEach(i => { i.required = false; i.disabled = true; });
        tfDiv.querySelectorAll('input[type="hidden"]').forEach(i => i.disabled = false);
    } else {
        choiceDiv.classList.remove('d-none');
        tfDiv.classList.add('d-none');
        choiceDiv.querySelectorAll('input[type="text"]').forEach(i => i.disabled = false);
        tfDiv.querySelectorAll('input[type="hidden"]').forEach(i => i.disabled = true);
        choiceDiv.querySelectorAll('input[type="text"]')[0].required = true;
        choiceDiv.querySelectorAll('input[type="text"]')[1].required = true;
    }
}
</script>
@endpush
@endsection
