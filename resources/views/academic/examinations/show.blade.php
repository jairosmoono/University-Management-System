@extends('layouts.app')
@section('title', $examination->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $examination->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.examinations.index') }}">Examinations</a></li>
            <li class="breadcrumb-item active">{{ $examination->name }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <div class="d-flex gap-2">
        <a href="{{ route('academic.examinations.edit', $examination) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('academic.examinations.seating', $examination) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Seating Plan</a>
    </div>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Examination Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Course</dt><dd class="col-7">{{ optional(optional($examination->courseOffering)->course)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7"><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $examination->type)) }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Date</dt><dd class="col-7">{{ \Carbon\Carbon::parse($examination->exam_date)->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted fw-normal">Time</dt><dd class="col-7">{{ $examination->start_time }} – {{ $examination->end_time }}</dd>
                    <dt class="col-5 text-muted fw-normal">Venue</dt><dd class="col-7">{{ $examination->venue ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Total Marks</dt><dd class="col-7">{{ $examination->total_marks }}</dd>
                    <dt class="col-5 text-muted fw-normal">Invigilator</dt><dd class="col-7">{{ optional(optional($examination->invigilator)->user)->name ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Results</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Student</th><th>Score</th><th>Grade</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($examination->results as $result)
                            <tr>
                                <td>{{ optional($result->student)->full_name }}</td>
                                <td>{{ $result->exam_score ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ $result->grade ?? '—' }}</span></td>
                                <td><span class="badge bg-{{ $result->status === 'approved' ? 'success' : 'warning text-dark' }}">{{ ucfirst($result->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No results entered yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
