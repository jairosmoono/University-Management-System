@extends('layouts.app')
@section('title', 'Quiz Result')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Quiz Result</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
        <li class="breadcrumb-item"><a href="{{ route('elearning.show', $eLearningQuizAttempt->quiz->course) }}">{{ $eLearningQuizAttempt->quiz->course->courseOffering->course?->name }}</a></li>
        <li class="breadcrumb-item active">Result</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Score card --}}
        <div class="card border-0 shadow-sm mb-4 text-center">
            <div class="card-body py-5">
                <div class="mb-3">
                    @if($eLearningQuizAttempt->passed)
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 mb-3" style="width:80px;height:80px">
                        <i class="bi bi-patch-check-fill text-success fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-success">Congratulations!</h4>
                    <p class="text-muted">You passed the quiz.</p>
                    @else
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 mb-3" style="width:80px;height:80px">
                        <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                    </div>
                    <h4 class="fw-bold text-danger">Not Passed</h4>
                    <p class="text-muted">You didn't meet the passing score this time.</p>
                    @endif
                </div>

                <div class="display-4 fw-bold {{ $eLearningQuizAttempt->passed ? 'text-success' : 'text-danger' }} mb-1">
                    {{ number_format($eLearningQuizAttempt->score, 1) }}%
                </div>
                <div class="text-muted mb-4">Passing mark: {{ $eLearningQuizAttempt->quiz->passing_score }}%</div>

                <div class="row g-3 justify-content-center">
                    <div class="col-auto">
                        <div class="px-4 py-2 border rounded-3 text-center">
                            <div class="fw-bold">{{ $eLearningQuizAttempt->attempt_number }}</div>
                            <div class="text-muted small">Attempt #</div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="px-4 py-2 border rounded-3 text-center">
                            <div class="fw-bold">{{ $eLearningQuizAttempt->quiz->questions->count() }}</div>
                            <div class="text-muted small">Questions</div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="px-4 py-2 border rounded-3 text-center">
                            <div class="fw-bold">{{ $eLearningQuizAttempt->submitted_at?->format('d M Y H:i') }}</div>
                            <div class="text-muted small">Submitted</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Answer review --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h6 class="fw-semibold mb-0">Answer Review</h6>
            </div>
            <div class="card-body px-4 pb-4">
                @php $answers = $eLearningQuizAttempt->answers ?? []; @endphp
                @foreach($eLearningQuizAttempt->quiz->questions as $qi => $question)
                @php
                    $selectedId  = $answers[$question->id] ?? null;
                    $correctOpt  = $question->options->where('is_correct', true)->first();
                    $isCorrect   = $selectedId && (int)$selectedId === $correctOpt?->id;
                @endphp
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex gap-3 mb-2">
                        <span class="badge bg-{{ $isCorrect ? 'success' : 'danger' }} rounded-pill flex-shrink-0 mt-1">{{ $qi + 1 }}</span>
                        <div class="fw-semibold">{{ $question->question_text }}</div>
                    </div>
                    <div class="ms-4 ps-2">
                        @foreach($question->options as $opt)
                        @php
                            $isSelected = (int)$selectedId === $opt->id;
                            $class = '';
                            $icon  = 'circle text-muted';
                            if ($opt->is_correct) {
                                $class = 'text-success fw-semibold';
                                $icon  = 'check-circle-fill text-success';
                            } elseif ($isSelected && !$opt->is_correct) {
                                $class = 'text-danger text-decoration-line-through';
                                $icon  = 'x-circle-fill text-danger';
                            }
                        @endphp
                        <div class="d-flex align-items-center gap-2 py-1 {{ $class }}">
                            <i class="bi bi-{{ $icon }}"></i>
                            <span class="small">{{ $opt->option_text }}</span>
                            @if($isSelected && !$opt->is_correct)<span class="badge bg-danger ms-1 small">Your answer</span>@endif
                            @if($opt->is_correct)<span class="badge bg-success ms-1 small">Correct</span>@endif
                        </div>
                        @endforeach
                        @if(!$selectedId)
                        <div class="text-warning small mt-1"><i class="bi bi-dash-circle me-1"></i>Not answered</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('elearning.show', $eLearningQuizAttempt->quiz->course) }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-1"></i>Back to Course
            </a>
            @if(!$eLearningQuizAttempt->passed && $eLearningQuizAttempt->quiz->canStudentAttempt($eLearningQuizAttempt->student_id))
            <a href="{{ route('elearning.quizzes.take', $eLearningQuizAttempt->quiz) }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-repeat me-1"></i>Retake Quiz
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
