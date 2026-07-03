@extends('layouts.app')
@section('title', 'Salary Advances')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Salary Advances</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Salary Advances</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdvanceModal">
        <i class="bi bi-plus-circle me-1"></i> New Request
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3"><i class="bi bi-list-ul text-primary fs-4"></i></div>
                <div><div class="fw-bold fs-3 lh-1">{{ $stats['total'] }}</div><div class="text-muted small mt-1">Total</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3"><i class="bi bi-hourglass-split text-warning fs-4"></i></div>
                <div><div class="fw-bold fs-3 lh-1">{{ $stats['pending'] }}</div><div class="text-muted small mt-1">Pending</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3"><i class="bi bi-check-circle text-success fs-4"></i></div>
                <div><div class="fw-bold fs-3 lh-1">{{ $stats['approved'] }}</div><div class="text-muted small mt-1">Approved</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3"><i class="bi bi-x-circle text-danger fs-4"></i></div>
                <div><div class="fw-bold fs-3 lh-1">{{ $stats['rejected'] }}</div><div class="text-muted small mt-1">Rejected</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3"><i class="bi bi-cash text-info fs-4"></i></div>
                <div><div class="fw-bold fs-3 lh-1">{{ $stats['paid'] }}</div><div class="text-muted small mt-1">Paid</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-dark bg-opacity-10 p-3"><i class="bi bi-currency-dollar text-dark fs-4"></i></div>
                <div>
                    <div class="fw-bold lh-1" style="font-size:1.1rem">{{ formatCurrency($stats['total_approved_amount']) }}</div>
                    <div class="text-muted small mt-1">Total Approved</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['pending','approved','rejected','paid'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="employee_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected' : '' }}>{{ optional($emp->user)->name ?: 'Employee #'.$emp->id }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->anyFilled(['status','employee_id']))
            <div class="col-auto"><a href="{{ route('hr.salary-advances.index') }}" class="btn btn-sm btn-light">Clear</a></div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Employee</th><th>Requested</th><th>Approved</th><th>Reason</th><th>Repayment</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($advances as $adv)
                    <tr>
                        <td>{{ optional(optional($adv->employee)->user)->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ formatCurrency($adv->amount_requested) }}</td>
                        <td>{{ $adv->amount_approved ? formatCurrency($adv->amount_approved) : '—' }}</td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($adv->reason, 40) }}</td>
                        <td class="small">
                            @if($adv->repayment_start_date)
                                From {{ $adv->repayment_start_date->format('M Y') }}<br>
                                <span class="text-muted">{{ $adv->repayment_months }} month(s)</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $adv->status === 'approved' ? 'success' : ($adv->status === 'rejected' ? 'danger' : ($adv->status === 'paid' ? 'info' : 'warning text-dark')) }}">
                                {{ ucfirst($adv->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($adv->status === 'pending')
                                <button class="btn btn-sm btn-outline-success me-1"
                                    onclick="openApproveModal({{ $adv->id }}, {{ $adv->amount_requested }})">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger me-1"
                                    onclick="openRejectModal({{ $adv->id }})">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @elseif($adv->status === 'approved')
                                <form method="POST" action="{{ route('hr.salary-advances.mark-paid', $adv) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-info me-1" title="Mark Paid"><i class="bi bi-cash"></i></button>
                                </form>
                            @endif
                            @if(in_array($adv->status, ['pending','rejected']))
                                <form method="POST" action="{{ route('hr.salary-advances.destroy', $adv) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this advance request?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No salary advance requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $advances->withQueryString()->links() }}

{{-- Add Modal --}}
<div class="modal fade" id="addAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hr.salary-advances.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">New Salary Advance Request</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">— Select Employee —</option>
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ optional($emp->user)->name ?: 'Employee #'.$emp->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Amount Requested <span class="text-danger">*</span></label>
                            <input type="number" name="amount_requested" class="form-control" required min="1" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Request Date <span class="text-danger">*</span></label>
                            <input type="date" name="request_date" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Repayment Period (months) <span class="text-danger">*</span></label>
                        <input type="number" name="repayment_months" class="form-control" required min="1" max="24" value="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="approveAdvanceForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-success"><i class="bi bi-check-circle me-2"></i>Approve Salary Advance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount to Approve <span class="text-danger">*</span></label>
                        <input type="number" name="amount_approved" id="approveAmount" class="form-control" required min="1" step="0.01">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Repayment Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="repayment_start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Repayment Months <span class="text-danger">*</span></label>
                            <input type="number" name="repayment_months" class="form-control" required min="1" max="24" value="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectAdvanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="rejectAdvanceForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-danger"><i class="bi bi-x-circle me-2"></i>Reject Salary Advance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="remarks" class="form-control" rows="3" required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openApproveModal(id, amount) {
    document.getElementById('approveAdvanceForm').action = '/hr/salary-advances/' + id + '/approve';
    document.getElementById('approveAmount').value       = amount;
    new bootstrap.Modal(document.getElementById('approveAdvanceModal')).show();
}
function openRejectModal(id) {
    document.getElementById('rejectAdvanceForm').action = '/hr/salary-advances/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectAdvanceModal')).show();
}
</script>
@endpush
@endsection
