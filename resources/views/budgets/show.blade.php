@extends('layouts.app')
@section('title', 'Budget — ' . $budget->department->name)
@section('page-title', 'Budget Detail')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-wallet2 me-2" style="color:var(--secondary)"></i>{{ $budget->department->name }} — {{ $budget->fiscal_year }}</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.budgets.index') }}">Budgets</a></li>
            <li class="breadcrumb-item active">{{ $budget->department->name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @if($budget->status === 'draft')
        <form action="{{ route('academic.budgets.approve', $budget) }}" method="POST" onsubmit="return confirm('Approve and activate this budget?')">
            @csrf
            <button class="btn btn-success"><i class="bi bi-check2-circle me-1"></i>Approve</button>
        </form>
        <form action="{{ route('academic.budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Delete this budget?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger">Delete</button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    @php $pct = $summary['used_percent']; $barColor = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : 'success'); @endphp
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-5" style="color:var(--primary)">K {{ number_format($summary['total_budget'], 2) }}</div>
            <div class="text-muted" style="font-size:0.82rem">Total Budget</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-5 text-danger">K {{ number_format($summary['total_expenses'], 2) }}</div>
            <div class="text-muted" style="font-size:0.82rem">Total Expenses</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-5 {{ $summary['remaining'] < 0 ? 'text-danger' : 'text-success' }}">
                K {{ number_format($summary['remaining'], 2) }}
            </div>
            <div class="text-muted" style="font-size:0.82rem">Remaining</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center py-3">
            <div class="fw-bold fs-5 text-{{ $barColor }}">{{ $pct }}%</div>
            <div class="text-muted" style="font-size:0.82rem">Budget Used</div>
            <div class="progress mx-3 mt-1" style="height:6px">
                <div class="progress-bar bg-{{ $barColor }}" style="width:{{ min($pct,100) }}%"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Add Transaction --}}
    @if(in_array($budget->status, ['active', 'approved']))
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold"><i class="bi bi-plus-lg me-2"></i>Record Transaction</h5></div>
            <div class="card-body">
                <form action="{{ route('academic.budgets.transaction', $budget) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="expense">Expense</option>
                            <option value="allocation">Allocation</option>
                            <option value="adjustment">Adjustment</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <input type="text" name="category" list="catList" class="form-control" placeholder="e.g. Equipment" required>
                        <datalist id="catList">
                            @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Amount (K)</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reference No</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Optional"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Record</button>
                </form>
            </div>
        </div>

        {{-- Spending by Category --}}
        @if($summary['by_category']->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Spending by Category</h5></div>
            <div class="card-body p-0">
                @foreach($summary['by_category'] as $cat)
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                    <span style="font-size:0.85rem">{{ $cat->category }}</span>
                    <span class="fw-semibold text-danger" style="font-size:0.85rem">K {{ number_format($cat->total, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Transactions Table --}}
    <div class="col-lg-{{ in_array($budget->status, ['active','approved']) ? '8' : '12' }}">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-semibold">Transaction Log</h5>
                <div class="text-muted" style="font-size:0.82rem">{{ $budget->transactions->count() }} entries</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Ref</th><th class="text-end">Amount</th><th>By</th></tr>
                    </thead>
                    <tbody>
                        @forelse($budget->transactions->sortByDesc('transaction_date') as $tx)
                        @php
                            $txColor = match($tx->type) {
                                'expense' => 'text-danger', 'allocation' => 'text-success',
                                'adjustment' => 'text-info', default => 'text-warning'
                            };
                        @endphp
                        <tr>
                            <td style="font-size:0.82rem;white-space:nowrap">{{ $tx->transaction_date->format('d M Y') }}</td>
                            <td><span class="badge bg-{{ ['expense'=>'danger','allocation'=>'success','adjustment'=>'info','transfer'=>'warning'][$tx->type] ?? 'secondary' }}">{{ ucfirst($tx->type) }}</span></td>
                            <td style="font-size:0.83rem">{{ $tx->category }}</td>
                            <td style="font-size:0.82rem;max-width:180px" class="text-truncate text-muted" title="{{ $tx->description }}">{{ $tx->description ?? '—' }}</td>
                            <td style="font-size:0.78rem" class="text-muted">{{ $tx->reference_no ?? '—' }}</td>
                            <td class="text-end fw-semibold {{ $txColor }}" style="white-space:nowrap">K {{ number_format($tx->amount, 2) }}</td>
                            <td style="font-size:0.78rem" class="text-muted">{{ $tx->recordedBy?->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
