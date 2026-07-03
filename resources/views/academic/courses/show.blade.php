@extends('layouts.app')
@section('title', $course->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $course->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">{{ $course->code }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.courses.edit', $course) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Course Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Code</dt><dd class="col-7"><code>{{ $course->code }}</code></dd>
                    <dt class="col-5 text-muted fw-normal">Credits</dt><dd class="col-7">{{ $course->credits }}</dd>
                    <dt class="col-5 text-muted fw-normal">Level</dt><dd class="col-7">{{ $course->level ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7"><span class="badge bg-light text-dark">{{ ucfirst($course->course_type) }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Department</dt><dd class="col-7">{{ optional($course->department)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Faculty</dt><dd class="col-7">{{ optional(optional($course->department)->faculty)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt><dd class="col-7"><span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($course->status) }}</span></dd>
                </dl>
                @if($course->description)
                <hr><p class="text-muted small mb-0">{{ $course->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Course Offerings</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Semester/Term</th><th>Lecturer</th><th>Students</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($course->offerings as $offering)
                            <tr>
                                <td>{{ optional($offering->semester)->name }}</td>
                                <td>{{ optional(optional($offering->lecturer)->user)->name ?? '—' }}</td>
                                <td>{{ $offering->enrolled_students ?? 0 }} / {{ $offering->max_students }}</td>
                                <td><a href="{{ route('academic.course-offerings.show', $offering) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No offerings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Prerequisites --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                <h6 class="mb-0">Prerequisites</h6>
                @can('manage-academic')
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addPrereqForm">
                    <i class="bi bi-plus-lg me-1"></i>Add
                </button>
                @endcan
            </div>

            @can('manage-academic')
            <div class="collapse" id="addPrereqForm">
                <div class="card-body border-bottom">
                    <form action="{{ route('academic.courses.prerequisites.store', $course) }}" method="POST" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:0.85rem">Prerequisite Course</label>
                            <select name="prerequisite_course_id" class="form-select form-select-sm" required>
                                <option value="">— Select course —</option>
                                @foreach($allCourses->where('id', '!=', $course->id) as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem">Min Grade</label>
                            <select name="min_grade" class="form-select form-select-sm">
                                @foreach(['A','B','C','D','E','F'] as $g)
                                <option value="{{ $g }}" {{ $g === 'D' ? 'selected' : '' }}>{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            @endcan

            <div class="card-body p-0">
                @if($course->prerequisites->isEmpty())
                <p class="text-muted text-center py-3 mb-0" style="font-size:0.85rem">No prerequisites defined.</p>
                @else
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th style="font-size:0.82rem">Course</th><th style="font-size:0.82rem">Min Grade</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($course->prerequisites as $prereq)
                        <tr>
                            <td style="font-size:0.85rem">
                                <code>{{ $prereq->prerequisiteCourse->code }}</code>
                                <span class="text-muted ms-1">{{ $prereq->prerequisiteCourse->name }}</span>
                            </td>
                            <td><span class="badge bg-secondary">{{ $prereq->min_grade }}</span></td>
                            <td class="text-end">
                                @can('manage-academic')
                                <form action="{{ route('academic.courses.prerequisites.destroy', [$course, $prereq]) }}" method="POST"
                                      onsubmit="return confirm('Remove this prerequisite?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
