@extends('layouts.app')
@section('title', 'GPA Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">GPA Report — {{ $student->full_name }}</h4>
        <p class="text-muted mb-0"><code>{{ $student->student_number }}</code></p>
    </div>
</div>

@php $latest = $gpaRecords->last(); @endphp
@if($latest)
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold text-primary mb-0">{{ $latest->cgpa }}</h2>
            <small class="text-muted">Current CGPA</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold mb-0">{{ $latest->gpa }}</h2>
            <small class="text-muted">Latest GPA</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h2 class="fw-bold mb-0">{{ $latest->credit_hours_earned }}</h2>
            <small class="text-muted">Credits Earned</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h6 class="fw-bold mb-0 mt-1">{{ $latest->academic_standing }}</h6>
            <small class="text-muted">Academic Standing</small>
        </div>
    </div>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent"><h6 class="mb-0">GPA History</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Academic Year</th><th>Semester/Term</th><th>GPA</th><th>CGPA</th><th>Credits</th><th>Standing</th></tr></thead>
                <tbody>
                    @forelse($gpaRecords as $record)
                    <tr>
                        <td>{{ optional(optional($record->semester)->academicYear)->name }}</td>
                        <td>{{ optional($record->semester)->name }}</td>
                        <td class="fw-semibold">{{ $record->gpa }}</td>
                        <td class="fw-semibold text-primary">{{ $record->cgpa }}</td>
                        <td>{{ $record->credit_hours_earned }}</td>
                        <td>
                            <span class="badge bg-{{ $record->academic_standing === "Dean's List" ? 'success' : ($record->academic_standing === 'Good Standing' ? 'primary' : ($record->academic_standing === 'Probation' ? 'warning text-dark' : 'danger')) }}">
                                {{ $record->academic_standing }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No GPA records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
