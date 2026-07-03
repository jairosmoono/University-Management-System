@extends('layouts.app')
@section('title', $faculty->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $faculty->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.faculties.index') }}">Faculties</a></li>
            <li class="breadcrumb-item active">{{ $faculty->name }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.faculties.edit', $faculty) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Faculty Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Code</dt><dd class="col-7"><code>{{ $faculty->code }}</code></dd>
                    <dt class="col-5 text-muted fw-normal">Dean</dt><dd class="col-7">{{ $faculty->dean?->user?->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Departments</dt><dd class="col-7">{{ $faculty->departments->count() }}</dd>
                    <dt class="col-5 text-muted fw-normal">Students</dt><dd class="col-7">{{ $faculty->students->count() }}</dd>
                </dl>
                @if($faculty->description)
                <hr><p class="text-muted small mb-0">{{ $faculty->description }}</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Departments</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Code</th><th>Programs</th><th>Staff</th></tr></thead>
                        <tbody>
                            @forelse($faculty->departments as $dept)
                            <tr>
                                <td><a href="{{ route('academic.departments.show', $dept) }}" class="text-decoration-none">{{ $dept->name }}</a></td>
                                <td><code>{{ $dept->code }}</code></td>
                                <td>{{ $dept->programs->count() }}</td>
                                <td>{{ $dept->staff->count() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No departments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
