@extends('layouts.app')
@section('title', $isStudentView ? 'My Payment History' : 'Payment Records')
@section('page-title', $isStudentView ? 'My Payment History' : 'Payment Records')

@section('content')

@if($isStudentView)
{{-- ══════════════════════════════════════════════════════════════════════════
     STUDENT VIEW
══════════════════════════════════════════════════════════════════════════ --}}

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>My Payment History</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Payment History</li>
        </ol></nav>
    </div>
    <a href="{{ route('finance.billing.index') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-receipt me-1"></i> My Bills
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="stat-value text-success">{{ formatCurrency($stats['total_paid']) }}</div>
                    <div class="stat-label text-muted">Total Paid</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar-month"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ formatCurrency($stats['this_month']) }}</div>
                    <div class="stat-label text-muted">This Month</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-calendar-year"></i></div>
                <div>
                    <div class="stat-value text-info">{{ formatCurrency($stats['this_year']) }}</div>
                    <div class="stat-label text-muted">This Year</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-list-check"></i></div>
                <div>
                    <div class="stat-value text-secondary">{{ $stats['count'] }}</div>
                    <div class="stat-label text-muted">{{ $stats['count'] === 1 ? 'Payment' : 'Payments' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm"
                       value="{{ request('from_date') }}" placeholder="From date">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm"
                       value="{{ request('to_date') }}" placeholder="To date">
            </div>
            <div class="col-md-3">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    @foreach(['Airtel Money','MTN','Zamtel','Visa','Mastercard','Cash','Bank Transfer'] as $m)
                    <option value="{{ $m }}" {{ request('payment_method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="verified"  {{ request('status') == 'verified'  ? 'selected' : '' }}>Verified</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                    <option value="reversed"  {{ request('status') == 'reversed'  ? 'selected' : '' }}>Reversed</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Filter</button>
                @if(request()->hasAny(['from_date','to_date','payment_method','status']))
                <a href="{{ route('finance.payments.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Payments table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Reference</th>
                        <th>Semester/Term</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php $pc = ['verified' => 'success', 'pending' => 'warning', 'reversed' => 'danger']; @endphp
                    <tr class="{{ $payment->status === 'reversed' ? 'opacity-50' : '' }}">
                        <td class="ps-3">
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span>
                        </td>
                        <td><code style="font-size:0.8rem">{{ $payment->reference_number }}</code></td>
                        <td>
                            <small>{{ optional($payment->studentBill?->semester)->name ?? '—' }}</small>
                        </td>
                        <td class="fw-semibold {{ $payment->status === 'reversed' ? 'text-muted text-decoration-line-through' : 'text-success' }}">
                            {{ formatCurrency($payment->amount) }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $payment->payment_method }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $pc[$payment->status] ?? 'secondary' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            @if($payment->status === 'verified')
                            <a href="{{ route('finance.payments.receipt', $payment) }}"
                               class="btn btn-sm btn-outline-success me-1" target="_blank" title="View Receipt">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('finance.payments.receipt', $payment) }}?dl=1"
                               class="btn btn-sm btn-outline-primary" title="Download Receipt">
                                <i class="bi bi-download"></i>
                            </a>
                            @else
                            <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-clock-history fs-2 d-block mb-2 opacity-50"></i>
                            No payment records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payments->hasPages())
    <div class="card-footer bg-transparent">
        {{ $payments->links() }}
    </div>
    @endif
</div>


@else
{{-- ══════════════════════════════════════════════════════════════════════════
     ADMIN / FINANCE VIEW (original)
══════════════════════════════════════════════════════════════════════════ --}}

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Payment Records</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Payments</li>
        </ol></nav>
    </div>
    @can('manage-finance')
    <a href="{{ route('finance.payments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Record Payment
    </a>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <h4 class="text-success fw-bold">{{ formatCurrency($stats['today']) }}</h4>
            <small class="text-muted">Today's Collections</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <h4 class="text-primary fw-bold">{{ formatCurrency($stats['this_month']) }}</h4>
            <small class="text-muted">This Month</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <h4 class="text-info fw-bold">{{ formatCurrency($stats['this_year']) }}</h4>
            <small class="text-muted">This Year</small>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm"><div class="card-body text-center">
            <h4 class="text-warning fw-bold">{{ $stats['pending_count'] }}</h4>
            <small class="text-muted">Pending Verification</small>
        </div></div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}" placeholder="To">
            </div>
            <div class="col-md-2">
                <select name="payment_method" class="form-select form-select-sm">
                    <option value="">All Methods</option>
                    @foreach(['Airtel Money','MTN','Zamtel','Visa','Mastercard','Cash','Bank Transfer'] as $m)
                    <option value="{{ $m }}" {{ request('payment_method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reversed" {{ request('status') == 'reversed' ? 'selected' : '' }}>Reversed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('finance.payments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th><th>Reference</th><th>Student</th><th>Amount</th><th>Method</th><th>Status</th><th>Recorded By</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                    <td><code>{{ $payment->reference_number }}</code></td>
                    <td>{{ optional(optional(optional($payment->studentBill)->student)->user)->name ?? '—' }}</td>
                    <td class="fw-semibold text-success">{{ formatCurrency($payment->amount) }}</td>
                    <td><span class="badge bg-secondary">{{ $payment->payment_method }}</span></td>
                    <td>
                        @php $pc = ['verified'=>'success','pending'=>'warning','reversed'=>'danger'] @endphp
                        <span class="badge bg-{{ $pc[$payment->status] ?? 'secondary' }}">{{ ucfirst($payment->status) }}</span>
                    </td>
                    <td>{{ optional($payment->recordedBy)->name ?? '—' }}</td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                        @if($payment->status === 'verified')
                        <a href="{{ route('finance.payments.receipt', $payment) }}?dl=1" class="btn btn-sm btn-outline-success" title="Download Receipt">
                            <i class="bi bi-download"></i>
                        </a>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                @if($payment->status !== 'reversed')
                                <li><a class="dropdown-item" href="{{ route('finance.payments.receipt', $payment) }}" target="_blank"><i class="bi bi-eye me-2"></i>View Receipt</a></li>
                                <li><a class="dropdown-item" href="{{ route('finance.payments.receipt', $payment) }}?dl=1"><i class="bi bi-download me-2"></i>Download Receipt</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                @can('manage-finance')
                                @if($payment->status === 'pending')
                                <li>
                                    <form method="POST" action="{{ route('finance.payments.verify', $payment) }}">
                                        @csrf
                                        <button class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Verify</button>
                                    </form>
                                </li>
                                @endif
                                @if($payment->status !== 'reversed')
                                <li>
                                    <form method="POST" action="{{ route('finance.payments.reverse', $payment) }}" onsubmit="return confirm('Reverse this payment?')">
                                        @csrf
                                        <button class="dropdown-item text-danger"><i class="bi bi-arrow-counterclockwise me-2"></i>Reverse</button>
                                    </form>
                                </li>
                                @endif
                                @endcan
                            </ul>
                        </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
