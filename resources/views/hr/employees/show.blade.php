@extends('layouts.app')
@section('title', $employee->full_name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $employee->full_name }}</h4>
        <p class="text-muted mb-0">{{ $employee->employee_id }} &bull; {{ $employee->designation }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        @hasrole('super-admin')
        <form method="POST" action="{{ route('hr.employees.destroy', $employee) }}" onsubmit="return confirm('Permanently delete {{ addslashes($employee->full_name) }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
        @endhasrole
    </div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">ID</dt><dd class="col-7"><code>{{ $employee->employee_id }}</code></dd>
                    <dt class="col-5 text-muted fw-normal">Department</dt><dd class="col-7">{{ optional($employee->department)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Designation</dt><dd class="col-7">{{ $employee->designation ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</dd>
                    <dt class="col-5 text-muted fw-normal">Joined</dt><dd class="col-7">{{ $employee->join_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Email</dt><dd class="col-7" style="word-break:break-all">{{ optional($employee->user)->email ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Phone</dt><dd class="col-7">{{ optional($employee->user)->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Salary</dt><dd class="col-7">K {{ number_format($employee->basic_salary, 2) }}</dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt><dd class="col-7"><span class="badge bg-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->status) }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><h6 class="mb-0">Leave Requests</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Type</th><th>From</th><th>To</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($employee->leaveRequests as $leave)
                            <tr>
                                <td>{{ optional($leave->leaveType)->name }}</td>
                                <td>{{ $leave->start_date instanceof \Carbon\Carbon ? $leave->start_date->format('d M Y') : $leave->start_date }}</td>
                                <td>{{ $leave->end_date instanceof \Carbon\Carbon ? $leave->end_date->format('d M Y') : $leave->end_date }}</td>
                                <td><span class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($leave->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-2">No leave requests.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
