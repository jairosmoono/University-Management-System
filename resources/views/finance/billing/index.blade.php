@extends('layouts.app')
@section('title', 'Student Bills')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Student Bills</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Student Bills</li>
        </ol></nav>
    </div>
    @can('manage-finance')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateBillsModal">
        <i class="bi bi-receipt me-1"></i> Generate Bills
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="text-primary fw-bold">{{ $stats['total'] }}</h4>
                <small class="text-muted">Total Bills</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="text-success fw-bold">{{ formatCurrency($stats['total_amount']) }}</h4>
                <small class="text-muted">Total Billed</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="text-info fw-bold">{{ formatCurrency($stats['amount_paid']) }}</h4>
                <small class="text-muted">Amount Paid</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="text-danger fw-bold">{{ formatCurrency($stats['outstanding']) }}</h4>
                <small class="text-muted">Outstanding</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search student..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('finance.billing.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Student ID</th><th>Student Name</th><th>Program</th><th>Semester/Term</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bills as $bill)
                <tr>
                    <td><code>{{ optional($bill->student)->student_id }}</code></td>
                    <td>{{ optional(optional($bill->student)->user)->name }}</td>
                    <td>{{ optional(optional($bill->student)->program)->name }}</td>
                    <td>{{ optional($bill->semester)->name }}</td>
                    <td>{{ formatCurrency($bill->total_amount) }}</td>
                    <td class="text-success">{{ formatCurrency($bill->amount_paid) }}</td>
                    <td class="text-danger fw-semibold">{{ formatCurrency($bill->balance) }}</td>
                    <td>
                        @php $sc = ['paid'=>'success','partial'=>'warning','unpaid'=>'danger'] @endphp
                        <span class="badge bg-{{ $sc[$bill->status] ?? 'secondary' }}">{{ ucfirst($bill->status) }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('finance.billing.show', $bill) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                @can('manage-finance')
                                <li><a class="dropdown-item" href="{{ route('finance.payments.create', $bill) }}"><i class="bi bi-cash me-2"></i>Add Payment</a></li>
                                @endcan
                                <li><a class="dropdown-item" href="{{ route('finance.billing.invoice', $bill) }}"><i class="bi bi-file-pdf me-2"></i>Download Invoice</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-finance')
<div class="modal fade" id="generateBillsModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('finance.billing.generate') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Generate Bills</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="text-muted small">This will create bills for all eligible students based on the selected fee structure.</p>
                    <div class="mb-3">
                        <label class="form-label">Academic Year *</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester/Term *</label>
                        <select name="semester_id" class="form-select" required>
                            <option value="">Select</option>
                            @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fee Structure *</label>
                        <select name="fee_structure_id" class="form-select" required>
                            <option value="">Select Fee Structure</option>
                            @foreach($feeStructures as $fs)
                            <option value="{{ $fs->id }}">{{ $fs->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Bills</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
