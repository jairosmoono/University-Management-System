@extends('layouts.app')
@section('title', $department->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $department->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.departments.index') }}">Departments</a></li>
            <li class="breadcrumb-item active">{{ $department->name }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.departments.edit', $department) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Department Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Code</dt><dd class="col-7"><code>{{ $department->code }}</code></dd>
                    <dt class="col-5 text-muted fw-normal">Faculty</dt><dd class="col-7">{{ optional($department->faculty)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">HOD</dt><dd class="col-7">{{ optional(optional($department->hod)->user)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Programs</dt><dd class="col-7">{{ $department->programs->count() }}</dd>
                    <dt class="col-5 text-muted fw-normal">Students</dt><dd class="col-7">{{ $department->students->count() }}</dd>
                    <dt class="col-5 text-muted fw-normal">Staff</dt><dd class="col-7">{{ $department->staff->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Programs</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Degree</th><th>Duration</th></tr></thead>
                        <tbody>
                            @forelse($department->programs as $prog)
                            <tr>
                                <td><a href="{{ route('academic.programs.show', $prog) }}" class="text-decoration-none">{{ $prog->name }}</a></td>
                                <td><code>{{ $prog->code }}</code></td>
                                <td>{{ $prog->degree_type }}</td>
                                <td>{{ $prog->duration_years }} yr(s)</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No programs yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Courses</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Credits</th><th>Level</th></tr></thead>
                        <tbody>
                            @forelse($department->courses as $course)
                            <tr>
                                <td><code>{{ $course->code }}</code></td>
                                <td>{{ $course->name }}</td>
                                <td>{{ $course->credit_hours }}</td>
                                <td>{{ $course->level }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No courses yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
