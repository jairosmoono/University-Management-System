@extends('layouts.app')
@section('title', $course->courseOffering->course->name ?? 'E-Learning Course')

@section('content')
@php
    $isStudent = auth()->user()->hasRole('student');
    $isAdmin   = $isAdmin ?? auth()->user()->hasRole('super-admin|registrar');
@endphp

<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $course->courseOffering->course?->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
            <li class="breadcrumb-item active">{{ $course->courseOffering->course?->code }}</li>
        </ol></nav>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($isStudent)
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Progress:</span>
            <div class="progress" style="width:120px;height:8px"><div class="progress-bar bg-success" style="width:{{ $progress }}%"></div></div>
            <span class="fw-semibold small">{{ $progress }}%</span>
        </div>
        @else
        <span class="badge bg-{{ $course->is_published ? 'success' : 'secondary' }} px-3 py-2">
            {{ $course->is_published ? 'Published' : 'Draft' }}
        </span>
        @if($isAdmin)
        <form method="POST" action="{{ route('elearning.toggle-publish', $course) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-{{ $course->is_published ? 'warning' : 'success' }}">
                <i class="bi bi-{{ $course->is_published ? 'eye-slash' : 'check-circle' }} me-1"></i>
                {{ $course->is_published ? 'Unpublish' : 'Publish' }}
            </button>
        </form>
        @else
        <a href="{{ route('elearning.quizzes.create', $course) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-patch-question me-1"></i>Add Quiz
        </a>
        @endif
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(!$isStudent)
{{-- ══════════════════════════════════════════
     LECTURER VIEW
════════════════════════════════════════════ --}}

{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-primary">{{ $course->lessons->count() }}</div>
            <div class="text-muted small">Lessons</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning">{{ $course->quizzes->count() }}</div>
            <div class="text-muted small">Quizzes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success">{{ $enrolledCount }}</div>
            <div class="text-muted small">Enrolled Students</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-info">{{ $attemptsCount }}</div>
            <div class="text-muted small">Quiz Attempts</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Lessons management --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Lessons</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addLessonForm">
                    <i class="bi bi-plus-lg me-1"></i>Add Lesson
                </button>
            </div>

            <div class="collapse px-4 pt-0 pb-3" id="addLessonForm">
                <div class="border rounded-3 p-3 bg-light">
                    <form method="POST" action="{{ route('elearning.lessons.store', $course) }}">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="title" class="form-control form-control-sm" placeholder="Lesson title *" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="description" rows="2" class="form-control form-control-sm" placeholder="Brief description (optional)"></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check form-check-inline small">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="lpub" checked>
                                <label class="form-check-label" for="lpub">Publish now</label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Add Lesson</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="list-group list-group-flush">
                @forelse($course->lessons as $lesson)
                <div class="list-group-item px-4 py-3">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary rounded-pill">{{ $loop->iteration }}</span>
                            <div>
                                <div class="fw-semibold">{{ $lesson->title }}</div>
                                @if($lesson->description)
                                <div class="text-muted small">{{ Str::limit($lesson->description, 80) }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <span class="badge bg-{{ $lesson->is_published ? 'success' : 'secondary' }}">
                                {{ $lesson->is_published ? 'Live' : 'Draft' }}
                            </span>
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                                data-bs-target="#lesson{{ $lesson->id }}Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('elearning.lessons.destroy', $lesson) }}"
                                onsubmit="return confirm('Delete this lesson and all its content?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>

                    {{-- Lesson items --}}
                    @if($lesson->items->count())
                    <div class="ms-4 mb-2">
                        @foreach($lesson->items as $item)
                        <div class="d-flex align-items-center justify-content-between gap-2 py-1 border-bottom">
                            <div class="d-flex align-items-center gap-2 small">
                                <i class="bi {{ \App\Models\ELearningLessonItem::typeIcon($item->content_type) }}"></i>
                                <span>{{ $item->title }}</span>
                                <span class="text-muted">({{ \App\Models\ELearningLessonItem::typeLabel($item->content_type) }})</span>
                            </div>
                            <form method="POST" action="{{ route('elearning.items.destroy', $item) }}" onsubmit="return confirm('Remove this item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Add item form --}}
                    <div class="ms-4">
                        <button class="btn btn-link btn-sm text-primary p-0" data-bs-toggle="collapse"
                            data-bs-target="#addItem{{ $lesson->id }}">
                            <i class="bi bi-plus-circle me-1"></i>Add Content
                        </button>
                        <div class="collapse mt-2" id="addItem{{ $lesson->id }}">
                            <form method="POST" action="{{ route('elearning.items.store', $lesson) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="border rounded-3 p-3 bg-light">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <input type="text" name="title" class="form-control form-control-sm" placeholder="Item title *" required>
                                        </div>
                                        <div class="col-12">
                                            <select name="content_type" class="form-select form-select-sm" id="ctype{{ $lesson->id }}"
                                                onchange="toggleContentInput(this, {{ $lesson->id }})">
                                                <option value="video_url">Video (YouTube/MP4 URL)</option>
                                                <option value="pdf_upload">PDF Upload</option>
                                                <option value="text_html">Text / Reading</option>
                                                <option value="external_link">External Link</option>
                                            </select>
                                        </div>
                                        <div class="col-12" id="url_input_{{ $lesson->id }}">
                                            <input type="text" name="content_url" class="form-control form-control-sm" placeholder="Paste YouTube or MP4 URL…">
                                        </div>
                                        <div class="col-12 d-none" id="pdf_input_{{ $lesson->id }}">
                                            <input type="file" name="pdf_file" class="form-control form-control-sm" accept=".pdf">
                                        </div>
                                        <div class="col-12 d-none" id="text_input_{{ $lesson->id }}">
                                            <textarea name="content_text" class="form-control form-control-sm" rows="4"
                                                placeholder="Enter content text or HTML…"></textarea>
                                        </div>
                                        <div class="col-12 d-none" id="link_input_{{ $lesson->id }}">
                                            <input type="text" name="content_link" class="form-control form-control-sm" placeholder="Paste external URL…">
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-primary btn-sm">Add Item</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Edit lesson collapse --}}
                    <div class="collapse mt-2" id="lesson{{ $lesson->id }}Edit">
                        <form method="POST" action="{{ route('elearning.lessons.update', $lesson) }}" class="border rounded-3 p-3 bg-light">
                            @csrf @method('PUT')
                            <div class="mb-2">
                                <input type="text" name="title" class="form-control form-control-sm" value="{{ $lesson->title }}" required>
                            </div>
                            <div class="mb-2">
                                <textarea name="description" class="form-control form-control-sm" rows="2">{{ $lesson->description }}</textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check form-check-inline small">
                                    <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                        id="ledit_pub_{{ $lesson->id }}" {{ $lesson->is_published ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ledit_pub_{{ $lesson->id }}">Published</label>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-4">
                    No lessons yet. Add your first lesson above.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Course settings + Quizzes --}}
    <div class="col-lg-5">

        {{-- Course settings --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4">
                <h6 class="fw-semibold mb-0">Course Settings</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('elearning.update', $course) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" rows="3" class="form-control form-control-sm">{{ $course->description }}</textarea>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <label class="form-label small fw-semibold mb-0">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                id="coursePublish" {{ $course->is_published ? 'checked' : '' }}>
                            <label class="form-check-label small" for="coursePublish">
                                {{ $course->is_published ? 'Published' : 'Draft' }}
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary w-100">Save Settings</button>
                </form>
                <hr class="my-3">
                <form method="POST" action="{{ route('elearning.destroy', $course) }}"
                    onsubmit="return confirm('Delete this entire e-learning course and all its content?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Delete Course
                    </button>
                </form>
            </div>
        </div>

        {{-- Quizzes --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">Quizzes</h6>
                <a href="{{ route('elearning.quizzes.create', $course) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-plus-lg"></i>
                </a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($course->quizzes as $quiz)
                <a href="{{ route('elearning.quizzes.show', $quiz) }}" class="list-group-item list-group-item-action px-4 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold small">{{ $quiz->title }}</div>
                            <div class="text-muted" style="font-size:0.78rem">
                                {{ $quiz->questions->count() }} questions &bull; Pass: {{ $quiz->passing_score }}%
                            </div>
                        </div>
                        <span class="badge bg-{{ $quiz->is_published ? 'success' : 'secondary' }}">
                            {{ $quiz->is_published ? 'Live' : 'Draft' }}
                        </span>
                    </div>
                </a>
                @empty
                <div class="list-group-item text-center text-muted py-3 small">No quizzes yet.</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@else
{{-- ══════════════════════════════════════════
     STUDENT VIEW
════════════════════════════════════════════ --}}

@if($course->description)
<div class="alert alert-light border mb-4">
    <i class="bi bi-info-circle me-2 text-primary"></i>{{ $course->description }}
</div>
@endif

<div class="row g-4">
    {{-- Lessons --}}
    <div class="col-lg-8">
        <div class="accordion" id="lessonsAccordion">
            @forelse($course->lessons->where('is_published', true) as $lesson)
            @php $done = in_array($lesson->id, $completedLessonIds); @endphp
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3 overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} py-3" type="button"
                        data-bs-toggle="collapse" data-bs-target="#lesson{{ $lesson->id }}">
                        <div class="d-flex align-items-center gap-3 w-100 me-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                                {{ $done ? 'bg-success' : 'bg-secondary' }} bg-opacity-15"
                                style="width:32px;height:32px">
                                <i class="bi bi-{{ $done ? 'check-lg text-success' : 'collection text-secondary' }} small"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $loop->iteration }}. {{ $lesson->title }}</div>
                                @if($lesson->description)
                                <div class="text-muted small fw-normal">{{ Str::limit($lesson->description, 70) }}</div>
                                @endif
                            </div>
                            <div class="ms-auto me-2">
                                @if($done)
                                <span class="badge bg-success">Completed</span>
                                @else
                                <span class="badge bg-light text-muted border">{{ $lesson->items->count() }} items</span>
                                @endif
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="lesson{{ $lesson->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                    data-bs-parent="#lessonsAccordion">
                    <div class="accordion-body pt-0 px-4 pb-4">
                        @forelse($lesson->items as $item)
                        <div class="border rounded-3 p-3 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi {{ \App\Models\ELearningLessonItem::typeIcon($item->content_type) }} fs-5"></i>
                                <span class="fw-semibold">{{ $item->title }}</span>
                                <span class="badge bg-light text-muted border ms-auto">{{ \App\Models\ELearningLessonItem::typeLabel($item->content_type) }}</span>
                            </div>

                            @if($item->content_type === 'video_url')
                            @php
                                $url = $item->content;
                                $embedUrl = null;
                                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_\-]{11})/', $url, $m)) {
                                    $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                                }
                            @endphp
                            @if($embedUrl)
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $embedUrl }}" allowfullscreen></iframe>
                            </div>
                            @else
                            <video controls class="w-100 rounded">
                                <source src="{{ $url }}">
                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">Open Video</a>
                            </video>
                            @endif

                            @elseif($item->content_type === 'pdf_upload')
                            <a href="{{ route('elearning.items.file', $item) }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Open PDF
                            </a>

                            @elseif($item->content_type === 'text_html')
                            <div class="prose" style="line-height:1.7">
                                {!! nl2br(e($item->content)) !!}
                            </div>

                            @elseif($item->content_type === 'external_link')
                            <a href="{{ $item->content }}" target="_blank" rel="noopener" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-link-45deg me-1"></i>Open Link
                            </a>
                            @endif
                        </div>
                        @empty
                        <p class="text-muted small">No content items in this lesson yet.</p>
                        @endforelse

                        @if(!$done)
                        <form method="POST" action="{{ route('elearning.lessons.complete', $lesson) }}" class="text-end">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Mark as Complete
                            </button>
                        </form>
                        @else
                        <div class="text-end">
                            <span class="text-success small"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4 text-muted">
                    <i class="bi bi-collection fs-2 mb-2 d-block"></i>
                    No lessons published yet.
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Sidebar: Quizzes --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3 pb-1 px-4">
                <h6 class="fw-semibold mb-0">Quizzes</h6>
            </div>
            <div class="list-group list-group-flush">
                @php $publishedQuizzes = $course->quizzes->where('is_published', true); @endphp
                @forelse($publishedQuizzes as $quiz)
                @php $bestAttempt = $quizAttempts[$quiz->id] ?? null; @endphp
                <div class="list-group-item px-4 py-3">
                    <div class="fw-semibold mb-1">{{ $quiz->title }}</div>
                    <div class="text-muted small mb-2">
                        Pass mark: {{ $quiz->passing_score }}% &bull;
                        {{ $quiz->max_attempts }} attempt(s) allowed
                        @if($quiz->time_limit_minutes)
                        &bull; {{ $quiz->time_limit_minutes }} min
                        @endif
                    </div>

                    @if($bestAttempt)
                    <div class="mb-2">
                        <span class="badge bg-{{ $bestAttempt->passed ? 'success' : 'danger' }} me-1">
                            {{ $bestAttempt->passed ? 'Passed' : 'Failed' }}
                        </span>
                        <span class="small text-muted">Best: {{ number_format($bestAttempt->score, 1) }}%</span>
                    </div>
                    @endif

                    @if($quiz->canStudentAttempt($student->id))
                    <a href="{{ route('elearning.quizzes.take', $quiz) }}" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-pencil me-1"></i>{{ $bestAttempt ? 'Retake Quiz' : 'Start Quiz' }}
                    </a>
                    @else
                    <button class="btn btn-sm btn-secondary w-100" disabled>Max attempts reached</button>
                    @endif
                </div>
                @empty
                <div class="list-group-item text-center text-muted py-3 small">No quizzes available yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function toggleContentInput(select, lessonId) {
    const type     = select.value;
    const urlDiv   = document.getElementById('url_input_'  + lessonId);
    const pdfDiv   = document.getElementById('pdf_input_'  + lessonId);
    const textDiv  = document.getElementById('text_input_' + lessonId);
    const linkDiv  = document.getElementById('link_input_' + lessonId);

    [urlDiv, pdfDiv, textDiv, linkDiv].forEach(d => d.classList.add('d-none'));

    urlDiv.querySelector('[name="content_url"]').required   = false;
    textDiv.querySelector('[name="content_text"]').required = false;
    linkDiv.querySelector('[name="content_link"]').required = false;
    pdfDiv.querySelector('[name="pdf_file"]').required      = false;

    if (type === 'video_url') {
        urlDiv.classList.remove('d-none');
        urlDiv.querySelector('[name="content_url"]').required = true;
    } else if (type === 'pdf_upload') {
        pdfDiv.classList.remove('d-none');
        pdfDiv.querySelector('[name="pdf_file"]').required = true;
    } else if (type === 'text_html') {
        textDiv.classList.remove('d-none');
        textDiv.querySelector('[name="content_text"]').required = true;
    } else if (type === 'external_link') {
        linkDiv.classList.remove('d-none');
        linkDiv.querySelector('[name="content_link"]').required = true;
    }
}
</script>
@endpush
@endsection
