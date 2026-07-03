@extends('layouts.app')
@section('title', 'Department Budgets')
@section('page-title', 'Department Budgets')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-wallet2 me-2" style="color:var(--secondary)"></i>Department Budgets</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Budgets</li>
        </ol></nav>
    </div>
    <a href="{{ route('academic.budgets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>New Budget
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-4" style="color:var(--primary)">{{ $totals->budget_count }}</div>
            <div class="text-muted" style="font-size:0.82rem">Total Budgets</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-4 text-success">K {{ number_format($totals->grand_total ?? 0, 2) }}</div>
            <div class="text-muted" style="font-size:0.82rem">Grand Total Budget</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fw-bold fs-4 text-info">{{ $departments->count() }}</div>
            <div class="text-muted" style="font-size:0.82rem">Departments</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-3">
        <h5 class="mb-0 fw-semibold flex-1">All Budgets</h5>
        <form class="d-flex gap-2 flex-wrap">
            <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width:180px">
                <option value="">All Departments</option>
                @foreach($departments as $d)
                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
            <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width:150px">
                <option value="">All Years</option>
                @foreach($academicYears as $y)
                <option value="{{ $y->id }}" {{ request('academic_year_id') == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width:130px">
                <option value="">All Statuses</option>
                @foreach(['draft','approved','active','closed'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Department</th>
                    <th>Fiscal Year</th>
                    <th>Total Budget</th>
                    <th>Spent</th>
                    <th>Remaining</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $budget)
                @php
                    $remaining = $budget->remaining_budget;
                    $pct = $budget->used_percent;
                    $barColor = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success');
                @endphp
                <tr>
                    <td>
                        <div class="fw-semibold" style="font-size:0.88rem">{{ $budget->department->name }}</div>
                        <div class="text-muted" style="font-size:0.78rem">{{ $budget->department->code ?? '' }}</div>
                    </td>
                    <td style="font-size:0.85rem">{{ $budget->fiscal_year }}</td>
                    <td class="fw-semibold" style="font-size:0.88rem">K {{ number_format($budget->total_budget, 2) }}</td>
                    <td class="text-danger" style="font-size:0.88rem">K {{ number_format($budget->total_expenses, 2) }}</td>
                    <td class="{{ $remaining < 0 ? 'text-danger fw-bold' : 'text-success' }}" style="font-size:0.88rem">
                        K {{ number_format($remaining, 2) }}
                    </td>
                    <td style="min-width:120px">
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-{{ $barColor }}" style="width:{{ min($pct,100) }}%"></div>
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;text-align:right">{{ $pct }}%</div>
                    </td>
                    <td>
                        @php $sc = ['draft'=>'secondary','approved'=>'info','active'=>'success','closed'=>'dark'][$budget->status] ?? 'secondary'; @endphp
                        <span class="badge bg-{{ $sc }}">{{ ucfirst($budget->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('academic.budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No budgets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($budgets->hasPages())
    <div class="card-footer py-2">{{ $budgets->links() }}</div>
    @endif
</div>
@endsection
