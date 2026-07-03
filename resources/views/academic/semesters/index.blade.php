@extends('layouts.app')
@section('title', 'Semesters/Terms')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Semesters/Terms</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Semesters/Terms</li>
            </ol>
        </nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.semesters.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Semester/Term
    </a>
    @endcan
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Academic Year</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Offerings</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semesters as $semester)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('academic.semesters.show', $semester) }}" class="fw-semibold text-decoration-none">
                                {{ $semester->name }}
                            </a>
                            @if($semester->is_current)
                                <span class="badge bg-success ms-1">Current</span>
                            @endif
                        </td>
                        <td>{{ optional($semester->academicYear)->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($semester->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($semester->end_date)->format('d M Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $semester->course_offerings_count }}</span></td>
                        <td>
                            <span class="badge bg-{{ $semester->status === 'active' ? 'success' : ($semester->status === 'completed' ? 'secondary' : 'warning text-dark') }}">
                                {{ ucfirst($semester->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('academic.semesters.show', $semester) }}" class="btn btn-sm btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                @can('manage-academic')
                                <a href="{{ route('academic.semesters.edit', $semester) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if(!$semester->is_current)
                                <form action="{{ route('academic.semesters.set-current', $semester) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Set as Current"><i class="bi bi-check2-circle"></i></button>
                                </form>
                                <form action="{{ route('academic.semesters.destroy', $semester) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this semester/term?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No semesters/terms found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
