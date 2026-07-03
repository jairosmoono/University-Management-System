@extends('layouts.app')
@section('title', 'E-Learning')

@section('content')
@php $isStudent = auth()->user()->hasRole('student'); @endphp

<div class="mb-4 d-flex align-items-start justify-content-between flex-wrap gap-2">
    <div>
        <h4 class="mb-1">{{ $isStudent ? 'My E-Learning Courses' : 'E-Learning Management' }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">E-Learning</li>
        </ol></nav>
    </div>
    @if(!$isStudent)
    <a href="{{ route('elearning.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Create Course
    </a>
    @endif
</div>

{{-- Lecturer: Stats + offerings list --}}
@if(!$isStudent)
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total_courses']) }}</div>
                <div class="text-muted small">Course Offerings</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ number_format($stats['with_elearning']) }}</div>
                <div class="text-muted small">With E-Learning</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-info">{{ number_format($stats['published']) }}</div>
                <div class="text-muted small">Published</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_lessons']) }}</div>
                <div class="text-muted small">Total Lessons</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($offerings as $offering)
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $offering->course?->name }}</h6>
                        <div class="text-muted small">{{ $offering->course?->code }} &bull; {{ $offering->semester?->name }}</div>
                    </div>
                    @if($offering->elearningCourse)
                        <span class="badge bg-{{ $offering->elearningCourse->is_published ? 'success' : 'secondary' }}">
                            {{ $offering->elearningCourse->is_published ? 'Live' : 'Draft' }}
                        </span>
                    @else
                        <span class="badge bg-light text-muted border">No Content</span>
                    @endif
                </div>

                @if($offering->elearningCourse)
                @php $elc = $offering->elearningCourse; @endphp
                <div class="d-flex gap-3 mb-3 small text-muted">
                    <span><i class="bi bi-collection me-1"></i>{{ $elc->lessons()->count() }} lessons</span>
                    <span><i class="bi bi-patch-question me-1"></i>{{ $elc->quizzes()->count() }} quizzes</span>
                </div>
                <a href="{{ route('elearning.show', $elc) }}" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-pencil-square me-1"></i>Manage Content
                </a>
                @else
                <p class="text-muted small mb-3">No e-learning content created yet.</p>
                <a href="{{ route('elearning.create') }}?offering_id={{ $offering->id }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i>Create E-Learning Course
                </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-laptop fs-1 text-muted mb-3 d-block"></i>
                <h6 class="fw-semibold">No course offerings yet</h6>
                <p class="text-muted small">You don't have any assigned course offerings this semester/term.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Student: enrolled e-learning courses --}}
@else
@if($courses->isNotEmpty())
<div class="row g-4">
    @foreach($courses as $row)
    @php $elc = $row['course']; $offering = $row['offering']; $progress = $row['progress']; @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column">
                <div class="mb-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $offering->course?->code }}</span>
                    <h6 class="fw-bold mb-1">{{ $offering->course?->name }}</h6>
                    <div class="text-muted small">{{ $offering->semester?->name }}</div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Progress</span>
                        <span class="fw-semibold">{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar bg-success" style="width:{{ $progress }}%"></div>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3 small text-muted">
                    <span><i class="bi bi-collection me-1"></i>{{ $elc->lessons()->where('is_published', true)->count() }} lessons</span>
                    <span><i class="bi bi-patch-question me-1"></i>{{ $elc->quizzes()->where('is_published', true)->count() }} quizzes</span>
                </div>

                <a href="{{ route('elearning.show', $elc) }}" class="btn btn-sm btn-primary mt-auto">
                    {{ $progress > 0 ? 'Continue Learning' : 'Start Learning' }}
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-laptop fs-1 text-muted mb-3 d-block"></i>
        <h6 class="fw-semibold">No e-learning courses available</h6>
        <p class="text-muted small">Your lecturers haven't created any e-learning content yet. Check back later.</p>
    </div>
</div>
@endif
@endif
@endsection
