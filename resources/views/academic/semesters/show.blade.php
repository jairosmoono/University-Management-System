@extends('layouts.app')
@section('title', $semester->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $semester->name }} @if($semester->is_current)<span class="badge bg-success ms-2">Current</span>@endif</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.semesters.index') }}">Semesters/Terms</a></li>
                <li class="breadcrumb-item active">{{ $semester->name }}</li>
            </ol>
        </nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.semesters.edit', $semester) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Semester/Term Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Name</dt>
                    <dd class="col-7">{{ $semester->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Academic Year</dt>
                    <dd class="col-7">{{ optional($semester->academicYear)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Start</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($semester->start_date)->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">End</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($semester->end_date)->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt>
                    <dd class="col-7">
                        <span class="badge bg-{{ $semester->status === 'active' ? 'success' : ($semester->status === 'completed' ? 'secondary' : 'warning text-dark') }}">
                            {{ ucfirst($semester->status) }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <h6 class="mb-0">Course Offerings ({{ $semester->courseOfferings->count() }})</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Code</th>
                                <th>Lecturer</th>
                                <th>Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semester->courseOfferings as $offering)
                            <tr>
                                <td>{{ optional($offering->course)->name }}</td>
                                <td><code>{{ optional($offering->course)->code }}</code></td>
                                <td>{{ optional(optional($offering->lecturer)->profile)->full_name ?? optional($offering->lecturer)->name }}</td>
                                <td>{{ $offering->capacity ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No course offerings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
