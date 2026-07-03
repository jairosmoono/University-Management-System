@extends('layouts.app')
@section('title', 'Payroll')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Payroll Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Payroll</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('hr.payroll.salary-schedule', request()->only(['month','year','department_id'])) }}"
           class="btn btn-outline-success btn-sm">
            <i class="bi bi-bank me-1"></i> Salary Schedule
        </a>
        <a href="{{ route('hr.payroll.report', request()->only(['month','year','department_id','status'])) }}"
           class="btn btn-outline-danger btn-sm">
            <i class="bi bi-file-pdf me-1"></i> Export PDF Report
        </a>
        @can('manage-hr')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
            <i class="bi bi-calculator me-1"></i> Generate Payroll
        </button>
        @endcan
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-primary bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-people text-primary fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['total_employees'] }}</div>
                    <div class="text-muted small mt-1">Employees</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['processed'] }}</div>
                    <div class="text-muted small mt-1">Processed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-warning bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $stats['pending'] }}</div>
                    <div class="text-muted small mt-1">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-info bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-wallet2 text-info fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1" style="font-size:0.95rem">{{ formatCurrency($stats['total_basic']) }}</div>
                    <div class="text-muted small mt-1">Basic Salary</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-dash-circle text-danger fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1" style="font-size:0.95rem">{{ formatCurrency($stats['total_deductions']) }}</div>
                    <div class="text-muted small mt-1">Deductions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-dark bg-opacity-10 p-3 flex-shrink-0">
                    <i class="bi bi-currency-dollar text-dark fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold lh-1" style="font-size:0.95rem">{{ formatCurrency($stats['total_payroll']) }}</div>
                    <div class="text-muted small mt-1">Net Payroll</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-2">
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month', date('n')) == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Emp. ID</th><th>Name</th><th>Department</th><th>Basic Salary</th><th>Allowances</th><th>Deductions</th><th>Net Pay</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payrolls as $payroll)
                <tr>
                    <td><code>{{ optional($payroll->employee)->employee_id }}</code></td>
                    <td class="fw-semibold">{{ optional(optional($payroll->employee)->user)->name }}</td>
                    <td>{{ optional(optional($payroll->employee)->department)->name }}</td>
                    <td>{{ formatCurrency($payroll->basic_salary) }}</td>
                    <td class="text-success">{{ formatCurrency($payroll->allowances ?? 0) }}</td>
                    <td class="text-danger">{{ formatCurrency($payroll->deductions ?? 0) }}</td>
                    <td class="fw-bold">{{ formatCurrency($payroll->net_pay) }}</td>
                    <td>
                        @php $sc = ['pending'=>'warning','processed'=>'info','paid'=>'success'] @endphp
                        <span class="badge bg-{{ $sc[$payroll->status] ?? 'secondary' }}">{{ ucfirst($payroll->status) }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('hr.payroll.slip', $payroll) }}"><i class="bi bi-file-pdf me-2"></i>Payslip PDF</a></li>
                                @can('manage-hr')
                                @if($payroll->status === 'pending')
                                <li>
                                    <form method="POST" action="{{ route('hr.payroll.process', $payroll) }}">
                                        @csrf
                                        <button class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Process</button>
                                    </form>
                                </li>
                                @endif
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $payrolls->withQueryString()->links() }}
    </div>
</div>

@can('manage-hr')
<div class="modal fade" id="generatePayrollModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hr.payroll.generate') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Generate Payroll</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Month *</label>
                            <select name="month" class="form-select" required>
                                @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Year *</label>
                            <select name="year" class="form-select" required>
                                @for($y = date('Y'); $y >= date('Y') - 1; $y--)
                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Department (blank for all)</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
