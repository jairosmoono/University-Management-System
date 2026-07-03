@extends('layouts.app')
@section('title', $program->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $program->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.programs.index') }}">Programs</a></li>
            <li class="breadcrumb-item active">{{ $program->code }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.programs.edit', $program) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Program Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Code</dt><dd class="col-7"><code>{{ $program->code }}</code></dd>
                    <dt class="col-5 text-muted fw-normal">Level</dt><dd class="col-7">{{ ucfirst($program->level) }}</dd>
                    <dt class="col-5 text-muted fw-normal">Duration</dt><dd class="col-7">{{ $program->duration_label }}</dd>
                    <dt class="col-5 text-muted fw-normal">Faculty</dt><dd class="col-7">{{ optional($program->faculty)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Department</dt><dd class="col-7">{{ optional($program->department)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Students</dt><dd class="col-7">{{ $program->students->count() }}</dd>
                    <dt class="col-5 text-muted fw-normal">Courses</dt><dd class="col-7">{{ $program->courses->count() }}</dd>
                </dl>
                @if($program->description)
                <hr><p class="text-muted small mb-0">{{ $program->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Courses in this Program</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Credits</th><th>Level</th><th>Type</th></tr></thead>
                        <tbody>
                            @forelse($program->courses as $course)
                            <tr>
                                <td><code>{{ $course->code }}</code></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credits }}</td>
                                <td>{{ $course->level ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $course->course_type }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No courses assigned yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
