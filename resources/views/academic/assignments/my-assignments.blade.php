@extends('layouts.app')
@section('title', 'My Assignments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">My Assignments</h4>
        <p class="text-muted mb-0">Published assignments for your registered courses</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@forelse($assignments as $a)
@php $sub = $mySubmissions[$a->id] ?? null; @endphp
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div>
                <h6 class="mb-1 fw-semibold">{{ $a->title }}</h6>
                <p class="text-muted mb-1" style="font-size:0.88rem">
                    <i class="bi bi-book me-1"></i>{{ optional(optional($a->courseOffering)->course)->code }} — {{ optional(optional($a->courseOffering)->course)->name }}
                </p>
                @if($a->description)
                <p class="mb-2" style="font-size:0.88rem">{{ Str::limit($a->description, 150) }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap" style="font-size:0.82rem">
                    <span class="text-{{ $a->is_overdue ? 'danger' : 'muted' }}">
                        <i class="bi bi-calendar-event me-1"></i>Due: {{ $a->due_date?->format('d M Y H:i') }}
                        @if($a->is_overdue)<strong>(Overdue)</strong>@endif
                    </span>
                    <span class="text-muted"><i class="bi bi-award me-1"></i>{{ $a->total_marks }} marks</span>
                </div>
            </div>
            <div class="text-end ms-3">
                @if(!$sub)
                    <span class="badge bg-warning text-dark mb-2 d-block">Not Submitted</span>
                    @if(!$a->is_overdue)
                    <a href="{{ route('academic.assignments.submit-form', $a) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-upload me-1"></i>Submit
                    </a>
                    @else
                    <span class="text-danger small">Submission closed</span>
                    @endif
                @elseif($sub->status === 'graded')
                    <span class="badge bg-success mb-2 d-block">Graded</span>
                    <div class="fw-bold fs-5">{{ $sub->marks_obtained }}<span class="text-muted fs-6">/{{ $a->total_marks }}</span></div>
                    <small class="text-muted">{{ $sub->percentage }}%</small>
                    <a href="{{ route('academic.assignments.submit-form', $a) }}" class="btn btn-outline-secondary btn-sm d-block mt-1">View Details</a>
                @elseif($sub->status === 'late')
                    <span class="badge bg-danger mb-2 d-block">Late Submission</span>
                    <a href="{{ route('academic.assignments.submit-form', $a) }}" class="btn btn-outline-secondary btn-sm d-block mt-1">View</a>
                @else
                    <span class="badge bg-primary mb-2 d-block">Submitted</span>
                    <small class="text-muted d-block">{{ $sub->submitted_at?->format('d M Y H:i') }}</small>
                    <a href="{{ route('academic.assignments.submit-form', $a) }}" class="btn btn-outline-primary btn-sm d-block mt-1">
                        <i class="bi bi-pencil me-1"></i>Edit Submission
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-25"></i>
        No assignments available for your courses yet.
    </div>
</div>
@endforelse
@endsection
