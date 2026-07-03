@extends('layouts.app')
@section('title', 'Salary Schedule')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Salary Payment Schedule</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.payroll.index') }}">Payroll</a></li>
            <li class="breadcrumb-item active">Salary Schedule</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('hr.payroll.salary-schedule') }}" class="d-flex gap-2">
            @foreach(request()->only(['month','year','department_id']) as $k => $v)
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <button type="submit" name="export" value="pdf" class="btn btn-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </button>
        </form>
        <a href="{{ route('hr.payroll.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1 text-muted">Month</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1 text-muted">Year</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1 text-muted">Department</label>
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('hr.payroll.salary-schedule') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-primary">
            <div class="fw-bold fs-4 text-primary">{{ $totalCount }}</div>
            <small class="text-muted">Employees on Schedule</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-success">
            <div class="fw-bold fs-4 text-success">{{ formatCurrency($totalNet) }}</div>
            <small class="text-muted">Total Net Payroll</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-info">
            <div class="fw-bold fs-4 text-info">{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</div>
            <small class="text-muted">Pay Period</small>
        </div>
    </div>
</div>

{{-- Schedule table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold">
            Salary Schedule &mdash; {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}
            @if($department) <span class="text-muted fw-normal">&bull; {{ $department->name }}</span> @endif
        </h6>
        <small class="text-muted">{{ $totalCount }} employees (processed &amp; paid only)</small>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th width="4%">#</th>
                    <th width="20%">Employee Name</th>
                    <th width="10%">Employee ID</th>
                    <th width="14%">Bank Name</th>
                    <th width="13%">Branch</th>
                    <th width="13%">Account Number</th>
                    <th width="12%">Sort Code</th>
                    <th width="14%" class="text-end">Net Pay</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $i => $p)
                <tr>
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ optional(optional($p->employee)->user)->name ?? '—' }}</td>
                    <td><code>{{ optional($p->employee)->employee_id ?? '—' }}</code></td>
                    <td>{{ optional($p->employee)->bank_name ?: '—' }}</td>
                    <td>{{ optional($p->employee)->bank_branch ?: '—' }}</td>
                    <td>
                        @php $acc = optional($p->employee)->bank_account; @endphp
                        @if($acc)
                            <code>{{ $acc }}</code>
                        @else
                            <span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Not set</span>
                        @endif
                    </td>
                    <td><code>{{ optional($p->employee)->sort_code ?: '—' }}</code></td>
                    <td class="text-end fw-bold text-success">{{ formatCurrency($p->net_pay) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No processed or paid payroll found for this period.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($payrolls->count())
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="7" class="text-end">TOTAL ({{ $totalCount }} employees)</td>
                    <td class="text-end text-success">{{ formatCurrency($totalNet) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
