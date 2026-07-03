@extends('layouts.app')
@section('title', $feeStructure->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $feeStructure->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.fee-structures.index') }}">Fee Structures</a></li>
            <li class="breadcrumb-item active">{{ $feeStructure->name }}</li>
        </ol></nav>
    </div>
    <a href="{{ route('finance.fee-structures.edit', $feeStructure) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-4 text-muted fw-normal">Name</dt><dd class="col-8">{{ $feeStructure->name }}</dd>
            <dt class="col-4 text-muted fw-normal">Type</dt><dd class="col-8"><span class="badge bg-secondary">{{ $feeStructure->type }}</span></dd>
            <dt class="col-4 text-muted fw-normal">Amount</dt><dd class="col-8 fw-bold">ZMW {{ number_format($feeStructure->amount, 2) }}</dd>
            <dt class="col-4 text-muted fw-normal">Academic Year</dt><dd class="col-8">{{ optional($feeStructure->academicYear)->name }}</dd>
            <dt class="col-4 text-muted fw-normal">Semester/Term</dt><dd class="col-8">{{ optional($feeStructure->semester)->name ?? 'All Semesters/Terms' }}</dd>
            <dt class="col-4 text-muted fw-normal">Program</dt><dd class="col-8">{{ optional($feeStructure->program)->name ?? 'All Programs' }}</dd>
            <dt class="col-4 text-muted fw-normal">Status</dt><dd class="col-8"><span class="badge bg-{{ $feeStructure->is_active ? 'success' : 'secondary' }}">{{ $feeStructure->is_active ? 'Active' : 'Inactive' }}</span></dd>
        </dl>
    </div>
</div>
@endsection
