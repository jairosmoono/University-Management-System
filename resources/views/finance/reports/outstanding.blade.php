@extends('layouts.app')
@section('title', 'Outstanding Balances Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Outstanding Balances Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.reports.index') }}">Finance Reports</a></li>
            <li class="breadcrumb-item active">Outstanding</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('finance.reports.outstanding') }}" class="d-flex gap-2">
            @foreach(request()->query() as $k => $v)
                @if($k !== 'export') <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endif
            @endforeach
            <button type="submit" name="export" value="pdf" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-pdf me-1"></i> Export PDF
            </button>
        </form>
        <a href="{{ route('finance.reports.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $s)
                    <option value="{{ $s->id }}" {{ request('semester_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                    <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-md-1">
                <a href="{{ route('finance.reports.outstanding') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-danger">
            <div class="fw-bold fs-4 text-danger">K {{ number_format($totals['total_outstanding'] ?? 0, 2) }}</div>
            <small class="text-muted">Total Outstanding</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-warning">
            <div class="fw-bold fs-4 text-warning">{{ $totals['unpaid_count'] ?? 0 }}</div>
            <small class="text-muted">Fully Unpaid Students</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-info">
            <div class="fw-bold fs-4 text-info">{{ $totals['partial_count'] ?? 0 }}</div>
            <small class="text-muted">Partial Payers</small>
        </div>
    </div>
</div>

<!-- Outstanding Bills Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between">
        <h6 class="mb-0 fw-semibold">Students with Outstanding Balances</h6>
        <small class="text-muted">{{ $bills->total() }} records</small>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Semester/Term</th>
                    <th>Total Billed</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bills as $bill)
                <tr class="{{ $bill->status === 'unpaid' ? 'table-danger' : 'table-warning' }} bg-opacity-25">
                    <td><a href="{{ route('finance.billing.show', $bill) }}" class="text-decoration-none fw-semibold">{{ $bill->student->student_id }}</a></td>
                    <td>{{ $bill->student->user->name }}</td>
                    <td><small>{{ optional($bill->student->program)->code ?? '—' }}</small></td>
                    <td><small>{{ optional($bill->semester)->name ?? '—' }}</small></td>
                    <td>K {{ number_format($bill->total_amount, 2) }}</td>
                    <td>K {{ number_format($bill->amount_paid, 2) }}</td>
                    <td class="fw-bold text-danger">K {{ number_format($bill->balance, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $bill->status === 'unpaid' ? 'danger' : 'warning text-dark' }}">
                            {{ ucfirst($bill->status) }}
                        </span>
                    </td>
                    <td>
                        @if($bill->due_date)
                            @php $overdue = \Carbon\Carbon::parse($bill->due_date)->isPast() @endphp
                            <span class="{{ $overdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                                {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                                @if($overdue) <i class="bi bi-clock-history text-danger ms-1" title="Overdue"></i> @endif
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                        No outstanding balances found
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($bills->count())
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4">TOTALS (this page)</td>
                    <td>K {{ number_format($bills->sum('total_amount'), 2) }}</td>
                    <td>K {{ number_format($bills->sum('amount_paid'), 2) }}</td>
                    <td class="text-danger">K {{ number_format($bills->sum('balance'), 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
        {{ $bills->links() }}
    </div>
</div>
@endsection
