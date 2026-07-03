@extends('layouts.app')
@section('title', 'Leave Management')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Leave Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Leave</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal"><i class="bi bi-plus-circle me-1"></i>Apply Leave</button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                    <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['total'] }}</div>
                    <div class="text-muted small mt-1">Total Requests</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['pending'] }}</div>
                    <div class="text-muted small mt-1">Pending</div>
                </div>
            </div>
            @if($stats['pending'] > 0)
            <div class="card-footer bg-warning bg-opacity-10 border-0 py-1 px-3">
                <a href="?status=pending" class="small text-warning text-decoration-none">View pending <i class="bi bi-arrow-right"></i></a>
            </div>
            @endif
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['approved'] }}</div>
                    <div class="text-muted small mt-1">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3">
                    <i class="bi bi-x-circle text-danger fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['rejected'] }}</div>
                    <div class="text-muted small mt-1">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary stats row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3">
                    <i class="bi bi-calendar-month text-info fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['this_month'] }}</div>
                    <div class="text-muted small mt-1">This Month</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="fw-semibold small text-muted mb-2">By Leave Type</div>
                @forelse($stats['by_type'] as $bt)
                @php
                    $pct = $stats['total'] > 0 ? round(($bt->total / $stats['total']) * 100) : 0;
                @endphp
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="text-truncate" style="min-width:100px;font-size:0.82rem">{{ optional($bt->leaveType)->name ?? 'Unknown' }}</div>
                    <div class="progress flex-grow-1" style="height:6px">
                        <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                    </div>
                    <span class="badge bg-light text-dark" style="font-size:0.75rem;min-width:2rem">{{ $bt->total }}</span>
                </div>
                @empty
                <p class="text-muted small mb-0">No data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Employee</th><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td>{{ optional(optional($leave->employee)->user)->name ?? '—' }}</td>
                        <td>{{ optional($leave->leaveType)->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->diffInDays($leave->end_date) + 1 }}</td>
                        <td><span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($leave->status) }}</span></td>
                        <td>
                            @if($leave->status === 'pending')
                            <div class="d-flex gap-1">
                                <form action="{{ route('hr.leave.approve', $leave) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Reject"
                                    onclick="openRejectModal({{ $leave->id }}, '{{ addslashes(optional(optional($leave->employee)->user)->name) }}', '{{ addslashes(optional($leave->leaveType)->name) }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            @elseif($leave->status === 'rejected' && $leave->remarks)
                            <small class="text-danger" title="{{ $leave->remarks }}">
                                <i class="bi bi-info-circle me-1"></i>{{ \Illuminate\Support\Str::limit($leave->remarks, 30) }}
                            </small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No leave requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $leaves->links() }}

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Apply for Leave</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('hr.leave.apply') }}" method="POST">
                @csrf
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
                    <div class="mb-3">
                        <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type_id" class="form-select" required>
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Rejection Reason Modal --}}
<div class="modal fade" id="rejectLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="rejectLeaveForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bi bi-x-circle me-2"></i>Reject Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border mb-3 py-2">
                        <small class="text-muted d-block">Employee</small>
                        <strong id="rejectEmpName">—</strong>
                        <small class="text-muted ms-2" id="rejectLeaveType"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="reason" id="rejectReason" class="form-control" rows="3"
                                  placeholder="Provide a clear reason for rejecting this leave request…" required minlength="5"></textarea>
                        <div class="form-text">This reason will be visible to the employee.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Confirm Rejection</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRejectModal(leaveId, empName, leaveType) {
    document.getElementById('rejectEmpName').textContent    = empName;
    document.getElementById('rejectLeaveType').textContent  = leaveType;
    document.getElementById('rejectReason').value           = '';
    document.getElementById('rejectLeaveForm').action       = '/hr/leave/' + leaveId + '/reject';
    new bootstrap.Modal(document.getElementById('rejectLeaveModal')).show();
    setTimeout(() => document.getElementById('rejectReason').focus(), 400);
}
</script>
@endpush

@endsection
