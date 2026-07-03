@extends('layouts.app')
@section('title', 'Academic Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Academic Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Academic</li>
        </ol></nav>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="fw-bold text-primary">{{ $stats['total_students'] ?? 0 }}</h3>
            <small class="text-muted">Active Students</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="fw-bold">{{ $stats['avg_gpa'] ?? '—' }}</h3>
            <small class="text-muted">Average GPA</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="fw-bold">{{ $stats['pass_rate'] ?? '—' }}%</h3>
            <small class="text-muted">Pass Rate</small>
        </div>
    </div>
</div>
<div class="alert alert-info">Full academic report generation available soon. Use the individual module reports in the meantime.</div>
@endsection
