@extends('layouts.app')
@section('title', 'Employee Appointments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Employee Appointments</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Appointments</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
        <i class="bi bi-plus-circle me-1"></i> New Appointment
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([['Total', $stats['total'], 'primary', 'file-earmark-person'], ['Active', $stats['active'], 'success', 'check-circle'], ['Expired', $stats['expired'], 'warning', 'clock-history'], ['Terminated', $stats['terminated'], 'danger', 'x-circle']] as [$label, $count, $color, $icon])
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-{{ $color }} bg-opacity-10 p-3">
                    <i class="bi bi-{{ $icon }} text-{{ $color }} fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $count }}</div>
                    <div class="text-muted small mt-1">{{ $label }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach(['active','expired','terminated'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="employee_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id')==$emp->id ? 'selected' : '' }}>{{ optional($emp->user)->name ?: 'Employee #'.$emp->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id')==$dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->anyFilled(['status','employee_id','department_id']))
            <div class="col-auto"><a href="{{ route('hr.employee-appointments.index') }}" class="btn btn-sm btn-light">Clear</a></div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Employee</th><th>Position</th><th>Department</th><th>Type</th><th>Start Date</th><th>End Date</th><th>Salary</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ optional(optional($appt->employee)->user)->name ?? '—' }}</td>
                        <td class="fw-semibold">{{ $appt->position }}</td>
                        <td class="text-muted small">{{ optional($appt->department)->name ?? '—' }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($appt->contract_type) }}</span></td>
                        <td>{{ $appt->start_date->format('d M Y') }}</td>
                        <td>{{ $appt->end_date ? $appt->end_date->format('d M Y') : '<span class="text-muted">Open</span>' }}</td>
                        <td>{{ $appt->salary ? formatCurrency($appt->salary) : '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $appt->status === 'active' ? 'success' : ($appt->status === 'expired' ? 'warning text-dark' : 'danger') }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1" onclick="openEditAppt({{ $appt->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('hr.employee-appointments.destroy', $appt) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this appointment record?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">No appointment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $appointments->withQueryString()->links() }}

{{-- Add Modal --}}
<div class="modal fade" id="addAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('hr.employee-appointments.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">New Appointment Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    @include('hr.employee-appointments._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editApptForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Appointment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    @include('hr.employee-appointments._form', ['prefix' => 'e_'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const appointments = @json($appointments->keyBy('id'));

function openEditAppt(id) {
    const a = appointments[id];
    if (!a) return;
    document.getElementById('editApptForm').action          = '/hr/employee-appointments/' + id;
    document.getElementById('e_employee_id').value          = a.employee_id || '';
    document.getElementById('e_department_id').value        = a.department_id || '';
    document.getElementById('e_position').value             = a.position || '';
    document.getElementById('e_appointment_date').value     = a.appointment_date || '';
    document.getElementById('e_start_date').value           = a.start_date || '';
    document.getElementById('e_end_date').value             = a.end_date || '';
    document.getElementById('e_salary').value               = a.salary || '';
    document.getElementById('e_contract_type').value        = a.contract_type || 'permanent';
    document.getElementById('e_status').value               = a.status || 'active';
    document.getElementById('e_notes').value                = a.notes || '';
    new bootstrap.Modal(document.getElementById('editAppointmentModal')).show();
}
</script>
@endpush
@endsection
