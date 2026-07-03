@extends('layouts.app')
@section('title', 'Timetable Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Timetable Entry</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.timetable.index') }}">Timetable</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('academic.timetable.edit', $timetable) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-4 text-muted fw-normal">Course</dt><dd class="col-8">{{ optional(optional($timetable->courseOffering)->course)->name }}</dd>
            <dt class="col-4 text-muted fw-normal">Code</dt><dd class="col-8"><code>{{ optional(optional($timetable->courseOffering)->course)->code }}</code></dd>
            <dt class="col-4 text-muted fw-normal">Lecturer</dt><dd class="col-8">{{ optional(optional(optional($timetable->courseOffering)->lecturer)->user)->name ?? '—' }}</dd>
            <dt class="col-4 text-muted fw-normal">Day</dt><dd class="col-8">{{ ucfirst($timetable->day_of_week) }}</dd>
            <dt class="col-4 text-muted fw-normal">Time</dt><dd class="col-8">{{ $timetable->start_time }} – {{ $timetable->end_time }}</dd>
            <dt class="col-4 text-muted fw-normal">Room</dt><dd class="col-8">{{ $timetable->room ?? '—' }}</dd>
            <dt class="col-4 text-muted fw-normal">Type</dt><dd class="col-8"><span class="badge bg-light text-dark">{{ ucfirst($timetable->type ?? 'lecture') }}</span></dd>
        </dl>
    </div>
</div>
@endsection
