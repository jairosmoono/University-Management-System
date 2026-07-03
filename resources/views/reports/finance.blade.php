@extends('layouts.app')
@section('title', 'Finance Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Finance Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Finance</li>
        </ol></nav>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="fw-bold text-success">ZMW {{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
            <small class="text-muted">Total Revenue Collected</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="fw-bold text-danger">ZMW {{ number_format($stats['outstanding'] ?? 0, 2) }}</h3>
            <small class="text-muted">Outstanding Balance</small>
        </div>
    </div>
</div>
<div class="alert alert-info">Detailed finance reports are available in the Finance module under Reports.</div>
@endsection
