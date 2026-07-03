@extends('layouts.app')
@section('title', 'E-Learning Administration')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1">E-Learning Administration</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">E-Learning</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Platform Stats ────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total_courses']) }}</div>
                <div class="text-muted small">Total Courses</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success">{{ number_format($stats['published']) }}</div>
                <div class="text-muted small">Published</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-secondary">{{ number_format($stats['draft']) }}</div>
                <div class="text-muted small">Draft</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-info">{{ number_format($stats['total_lessons']) }}</div>
                <div class="text-muted small">Lessons</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['total_quizzes']) }}</div>
                <div class="text-muted small">Quizzes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['total_completions']) }}</div>
                <div class="text-muted small">Completions</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger">{{ number_format($stats['total_attempts']) }}</div>
                <div class="text-muted small">Quiz Attempts</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-{{ $stats['pass_rate'] >= 60 ? 'success' : 'warning' }}">{{ $stats['pass_rate'] }}%</div>
                <div class="text-muted small">Quiz Pass Rate</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Courses Table ─────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-laptop me-2 text-primary"></i>All E-Learning Courses</h6>
        <span class="badge bg-secondary">{{ $courses->count() }} courses</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Course</th>
                        <th>Faculty / Dept</th>
                        <th>Lecturer</th>
                        <th>Semester/Term</th>
                        <th class="text-center">Lessons</th>
                        <th class="text-center">Quizzes</th>
                        <th class="text-center">Enrolled</th>
                        <th class="text-center">Completions</th>
                        <th class="text-center">Quiz Attempts</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $elc)
                    @php
                        $offering = $elc->courseOffering;
                        $course   = $offering?->course;
                        $faculty  = $course?->department?->faculty;
                        $dept     = $course?->department;
                        $lecturer = $offering?->lecturer?->user;
                        $passRate = $elc->attempts_count > 0
                            ? round($elc->passed_count / $elc->attempts_count * 100)
                            : null;
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $course?->name ?? '—' }}</div>
                            <small class="text-muted">{{ $course?->code }}</small>
                        </td>
                        <td>
                            <div class="small">{{ $faculty?->name ?? '—' }}</div>
                            <small class="text-muted">{{ $dept?->name ?? '—' }}</small>
                        </td>
                        <td>
                            @if($lecturer)
                                <div class="small">{{ $lecturer->name }}</div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $offering?->semester?->name ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-75 text-dark">{{ $elc->lessons_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning bg-opacity-75 text-dark">{{ $elc->quizzes_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary bg-opacity-75">{{ $elc->enrolled_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-75">{{ $elc->completions_count }}</span>
                        </td>
                        <td class="text-center">
                            @if($elc->attempts_count > 0)
                                <span class="badge bg-secondary">{{ $elc->attempts_count }}</span>
                                @if($passRate !== null)
                                    <small class="text-muted d-block">{{ $passRate }}% passed</small>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($elc->is_published)
                                <span class="badge bg-success">Published</span>
                            @else
                                <span class="badge bg-secondary">Draft</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('elearning.show', $elc) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="View course">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('elearning.toggle-publish', $elc) }}" class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-{{ $elc->is_published ? 'warning' : 'success' }}"
                                            title="{{ $elc->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="bi bi-{{ $elc->is_published ? 'eye-slash' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('elearning.destroy', $elc) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this e-learning course and all its content? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-5">
                            <i class="bi bi-laptop fs-2 d-block mb-2"></i>
                            No e-learning courses have been created yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
