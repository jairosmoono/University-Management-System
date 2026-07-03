@extends('layouts.app')
@section('title', 'Attendance Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Attendance Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol></nav>
    </div>
</div>
<div class="alert alert-info">Detailed attendance reports are available in the Academic module under Attendance. Use the links below for quick access.</div>
<div class="d-flex gap-2">
    <a href="{{ route('academic.attendance.index') }}" class="btn btn-outline-primary"><i class="bi bi-calendar-check me-1"></i>Attendance Sessions</a>
    <a href="{{ route('academic.attendance.report') }}" class="btn btn-outline-secondary"><i class="bi bi-bar-chart me-1"></i>Course Report</a>
</div>
@endsection
