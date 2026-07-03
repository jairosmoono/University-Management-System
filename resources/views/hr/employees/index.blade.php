@extends('layouts.app')
@section('title', 'Employees')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Employees / Staff</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Employees</li>
        </ol></nav>
    </div>
    @can('manage-hr')
    <div class="d-flex gap-2">
        <a href="{{ route('hr.employees.bulk-upload') }}" class="btn btn-outline-success">
            <i class="bi bi-cloud-arrow-up me-1"></i> Bulk Upload
        </a>
        <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Employee
        </a>
    </div>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-primary fw-bold">{{ $stats['total'] }}</h4><small class="text-muted">Total Staff</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['permanent'] }}</h4><small class="text-muted">Permanent</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['contract'] }}</h4><small class="text-muted">Contract</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-info fw-bold">{{ $stats['on_leave'] }}</h4><small class="text-muted">On Leave</small></div></div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="employment_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="permanent" {{ request('employment_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="part-time" {{ request('employment_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Emp. ID</th><th>Name</th><th>Department</th><th>Designation</th><th>Type</th><th>Join Date</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td><code>{{ $emp->employee_id }}</code></td>
                    <td class="fw-semibold">{{ optional($emp->user)->name }}</td>
                    <td>{{ optional($emp->department)->name }}</td>
                    <td>{{ $emp->designation }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($emp->employment_type) }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($emp->join_date)->format('d M Y') }}</td>
                    <td><span class="badge bg-{{ $emp->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($emp->status) }}</span></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('hr.employees.show', $emp) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                @can('manage-hr')
                                <li><a class="dropdown-item" href="{{ route('hr.employees.edit', $emp) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="{{ route('hr.payroll.slip', $emp) }}"><i class="bi bi-file-pdf me-2"></i>Payslip</a></li>
                                @endcan
                                @hasrole('super-admin')
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('hr.employees.destroy', $emp) }}" onsubmit="return confirm('Permanently delete {{ addslashes(optional($emp->user)->name) }}? This cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                                @endhasrole
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $employees->withQueryString()->links() }}
    </div>
</div>
@endsection
