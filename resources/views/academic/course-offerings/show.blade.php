@extends('layouts.app')
@section('title', 'Course Offering')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ optional($courseOffering->course)->code }} — {{ optional($courseOffering->course)->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.course-offerings.index') }}">Course Offerings</a></li>
            <li class="breadcrumb-item active">{{ optional($courseOffering->semester)->name }}</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <a href="{{ route('academic.course-offerings.edit', $courseOffering) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Offering Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Semester/Term</dt><dd class="col-7">{{ optional($courseOffering->semester)->name }}</dd>
                    <dt class="col-5 text-muted fw-normal">Lecturer</dt><dd class="col-7">{{ optional(optional($courseOffering->lecturer)->user)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Room</dt><dd class="col-7">{{ $courseOffering->room ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Capacity</dt><dd class="col-7">{{ $courseOffering->enrolled_students ?? 0 }} / {{ $courseOffering->max_students }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent"><h6 class="mb-0">Enrolled Students ({{ $courseOffering->approvedRegistrations->count() }})</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>#</th><th>Student</th><th>ID</th><th>Program</th></tr></thead>
                        <tbody>
                            @forelse($courseOffering->approvedRegistrations as $reg)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ optional($reg->student)->full_name }}</td>
                                <td><code>{{ optional($reg->student)->student_number }}</code></td>
                                <td>{{ optional(optional($reg->student)->program)->code }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No students enrolled.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Timetable</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Day</th><th>Start</th><th>End</th><th>Room</th><th>Type</th></tr></thead>
                        <tbody>
                            @forelse($courseOffering->timetables as $tt)
                            <tr>
                                <td>{{ ucfirst($tt->day_of_week) }}</td>
                                <td>{{ $tt->start_time }}</td>
                                <td>{{ $tt->end_time }}</td>
                                <td>{{ $tt->room ?? '—' }}</td>
                                <td>{{ ucfirst($tt->type ?? 'lecture') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No timetable set.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
