@extends('layouts.app')
@section('title', 'Attendance Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Attendance Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.attendance.index') }}">Attendance</a></li>
            <li class="breadcrumb-item active">Report</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button></div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Course</th><th>Semester/Term</th><th>Lecturer</th><th>Total Sessions</th></tr>
                </thead>
                <tbody>
                    @forelse($offerings as $offering)
                    <tr>
                        <td>{{ optional($offering->course)->name }}</td>
                        <td>{{ optional($offering->semester)->name }}</td>
                        <td>{{ optional(optional($offering->lecturer)->user)->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ $offering->total_sessions }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No data found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
