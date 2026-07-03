@extends('layouts.app')
@section('title', $academicYear->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $academicYear->name }} @if($academicYear->is_current)<span class="badge bg-success ms-2">Current</span>@endif</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('academic.academic-years.index') }}">Academic Years</a></li>
                <li class="breadcrumb-item active">{{ $academicYear->name }}</li>
            </ol>
        </nav>
    </div>
    @can('manage-academic')
    <div class="d-flex gap-2">
        <a href="{{ route('academic.academic-years.edit', $academicYear) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
    @endcan
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Name</dt>
                    <dd class="col-7">{{ $academicYear->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Start</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($academicYear->start_date)->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">End</dt>
                    <dd class="col-7">{{ \Carbon\Carbon::parse($academicYear->end_date)->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt>
                    <dd class="col-7">
                        @if($academicYear->is_current)
                            <span class="badge bg-success">Current</span>
                        @else
                            <span class="badge bg-light text-dark">Inactive</span>
                        @endif
                    </dd>
                    <dt class="col-5 text-muted fw-normal">Semesters/Terms</dt>
                    <dd class="col-7">{{ $academicYear->semesters->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Semesters/Terms</h6>
                @can('manage-academic')
                <a href="{{ route('academic.semesters.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus me-1"></i>Add Semester/Term</a>
                @endcan
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($academicYear->semesters as $semester)
                            <tr>
                                <td>
                                    {{ $semester->name }}
                                    @if($semester->is_current)<span class="badge bg-success ms-1">Current</span>@endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($semester->start_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($semester->end_date)->format('d M Y') }}</td>
                                <td><span class="badge bg-{{ $semester->status === 'active' ? 'success' : ($semester->status === 'completed' ? 'secondary' : 'warning text-dark') }}">{{ ucfirst($semester->status) }}</span></td>
                                <td>
                                    <a href="{{ route('academic.semesters.show', $semester) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No semesters/terms yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
