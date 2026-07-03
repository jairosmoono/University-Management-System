@extends('layouts.app')
@section('title', 'Payroll Configuration')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Payroll Configuration</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.payroll.index') }}">Payroll</a></li>
            <li class="breadcrumb-item active">Configuration</li>
        </ol></nav>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs mb-4" id="configTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-rates"><i class="bi bi-percent me-1"></i>Tax & Statutory Rates</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-allowances"><i class="bi bi-plus-circle me-1"></i>Allowance Allocation</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-deductions"><i class="bi bi-dash-circle me-1"></i>Custom Deductions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-advances"><i class="bi bi-cash-coin me-1"></i>Advance Repayments</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-banks"><i class="bi bi-bank me-1"></i>Bank Accounts</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-types"><i class="bi bi-list-check me-1"></i>Others</a></li>
</ul>

<div class="tab-content">

    {{-- ── TAB 1: Rates ──────────────────────────────────────────────── --}}
    <div class="tab-pane fade show active" id="tab-rates">
        <form method="POST" action="{{ route('hr.payroll.config.global') }}">
            @csrf
            <div class="row g-4">

                {{-- PAYE --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center gap-2">
                            <span class="badge bg-danger fs-6">PAYE</span>
                            <span class="fw-semibold">Pay As You Earn — Income Tax Bands</span>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-light border small mb-3">
                                Tax is calculated on gross salary (basic + allowances). Band 1 income is tax-free; amounts above each threshold are taxed at the band rate.
                            </div>
                            @php $paye = $configs['paye'] ?? collect(); @endphp
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Setting</th><th style="width:140px">Value</th></tr>
                                </thead>
                                <tbody>
                                @foreach($paye as $cfg)
                                <tr>
                                    <td>
                                        <div class="fw-semibold small">{{ $cfg->label }}</div>
                                        @if($cfg->description)<div class="text-muted" style="font-size:0.78rem">{{ $cfg->description }}</div>@endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="configs[{{ $cfg->key }}]"
                                                   class="form-control text-end"
                                                   value="{{ $cfg->value }}"
                                                   step="0.01" min="0">
                                            <span class="input-group-text">{{ str_contains($cfg->key,'rate') ? '%' : 'K' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- NAPSA + NHIMA --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center gap-2">
                            <span class="badge bg-primary fs-6">NAPSA</span>
                            <span class="fw-semibold">National Pension Scheme Authority</span>
                        </div>
                        <div class="card-body">
                            @php $napsa = $configs['napsa'] ?? collect(); @endphp
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light"><tr><th>Setting</th><th style="width:140px">Value</th></tr></thead>
                                <tbody>
                                @foreach($napsa as $cfg)
                                <tr>
                                    <td>
                                        <div class="fw-semibold small">{{ $cfg->label }}</div>
                                        @if($cfg->description)<div class="text-muted" style="font-size:0.78rem">{{ $cfg->description }}</div>@endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="configs[{{ $cfg->key }}]"
                                                   class="form-control text-end"
                                                   value="{{ $cfg->value }}"
                                                   step="0.01" min="0">
                                            <span class="input-group-text">{{ str_contains($cfg->key,'rate') ? '%' : 'K' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center gap-2">
                            <span class="badge bg-success fs-6">NHIMA</span>
                            <span class="fw-semibold">National Health Insurance Management Authority</span>
                        </div>
                        <div class="card-body">
                            @php $nhima = $configs['nhima'] ?? collect(); @endphp
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light"><tr><th>Setting</th><th style="width:140px">Value</th></tr></thead>
                                <tbody>
                                @foreach($nhima as $cfg)
                                <tr>
                                    <td>
                                        <div class="fw-semibold small">{{ $cfg->label }}</div>
                                        @if($cfg->description)<div class="text-muted" style="font-size:0.78rem">{{ $cfg->description }}</div>@endif
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="configs[{{ $cfg->key }}]"
                                                   class="form-control text-end"
                                                   value="{{ $cfg->value }}"
                                                   step="0.01" min="0">
                                            <span class="input-group-text">{{ str_contains($cfg->key,'rate') ? '%' : 'K' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="form-text mt-1">Set cap to 0 for no cap.</div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Save Rate Configuration
                </button>
            </div>
        </form>
    </div>

    {{-- ── TAB 2: Allowances ─────────────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-allowances">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Allowance</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.payroll.config.allowances.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">— Select —</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ optional($emp->user)->name ?: 'Emp #'.$emp->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Allowance Type <span class="text-danger">*</span></label>
                                <select name="allowance_type" class="form-select" required>
                                    @foreach($allowanceTypes as $t)
                                    <option value="{{ $t->slug }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" maxlength="255">
                            </div>
                            <div class="mb-1">
                                <label class="form-label">Value <span class="text-danger">*</span> <small class="text-muted">(fill one)</small></label>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col">
                                    <div class="input-group">
                                        <input type="number" name="percentage" class="form-control" placeholder="0.00" min="0" max="100" step="0.01">
                                        <span class="input-group-text">% of basic</span>
                                    </div>
                                </div>
                                <div class="col-auto d-flex align-items-center text-muted">or</div>
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text">K</span>
                                        <input type="number" name="amount" class="form-control" placeholder="0.00" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Allowance</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-semibold">Configured Allowances</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Employee</th><th>Type</th><th>Description</th><th>Value</th><th>Active</th><th class="text-end">Actions</th></tr>
                                </thead>
                                <tbody>
                                    @php $hasAllowances = false; @endphp
                                    @foreach($employees as $emp)
                                        @foreach($emp->allowances as $al)
                                            @php $hasAllowances = true; @endphp
                                            <tr>
                                                <td class="fw-semibold small">{{ optional($emp->user)->name }}</td>
                                                <td><span class="badge bg-info">{{ optional($allowanceTypes->firstWhere('slug', $al->allowance_type))->name ?? ucfirst(str_replace('_',' ',$al->allowance_type)) }}</span></td>
                                                <td class="text-muted small">{{ $al->description ?: '—' }}</td>
                                                <td class="fw-semibold">
                                                    @if($al->percentage > 0)
                                                        {{ $al->percentage }}%
                                                    @else
                                                        {{ formatCurrency($al->amount) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $al->is_active ? 'success' : 'secondary' }}">
                                                        {{ $al->is_active ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-secondary me-1"
                                                        onclick="openEditAllowance({{ $al->id }}, {{ $al->employee_id }}, '{{ $al->allowance_type }}', '{{ addslashes($al->description ?? '') }}', {{ $al->percentage }}, {{ $al->amount }}, {{ $al->is_active ? 1 : 0 }})">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('hr.payroll.config.allowances.destroy', $al) }}" class="d-inline"
                                                          onsubmit="return confirm('Remove this allowance?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @if(!$hasAllowances)
                                    <tr><td colspan="6" class="text-center text-muted py-4">No allowances configured yet.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 3: Custom Deductions ──────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-deductions">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle me-2 text-danger"></i>Add Deduction</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.payroll.config.deductions.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">— Select —</option>
                                    @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ optional($emp->user)->name ?: 'Emp #'.$emp->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deduction Type <span class="text-danger">*</span></label>
                                <select name="deduction_type" class="form-select" required>
                                    @foreach($deductionTypes as $t)
                                    <option value="{{ $t->slug }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control" maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount (K) <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" required min="0.01" step="0.01">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="newDedRecurring" checked>
                                    <label class="form-check-label" for="newDedRecurring">Recurring (every payroll run)</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Add Deduction</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3">
                        <h6 class="mb-0 fw-semibold">Configured Deductions</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr><th>Employee</th><th>Type</th><th>Description</th><th>Amount</th><th>Recurring</th><th>Active</th><th class="text-end">Actions</th></tr>
                                </thead>
                                <tbody>
                                    @php $hasDeds = false; @endphp
                                    @foreach($employees as $emp)
                                        @foreach($emp->deductions as $ded)
                                            @php $hasDeds = true; @endphp
                                            <tr>
                                                <td class="fw-semibold small">{{ optional($emp->user)->name }}</td>
                                                <td><span class="badge bg-danger">{{ optional($deductionTypes->firstWhere('slug', $ded->deduction_type))->name ?? ucfirst(str_replace('_',' ',$ded->deduction_type)) }}</span></td>
                                                <td class="text-muted small">{{ $ded->description ?: '—' }}</td>
                                                <td class="fw-semibold text-danger">{{ formatCurrency($ded->amount) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $ded->is_recurring ? 'primary' : 'secondary' }}">
                                                        {{ $ded->is_recurring ? 'Yes' : 'One-off' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $ded->is_active ? 'success' : 'secondary' }}">
                                                        {{ $ded->is_active ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-secondary me-1"
                                                        onclick="openEditDeduction({{ $ded->id }}, '{{ $ded->deduction_type }}', '{{ addslashes($ded->description ?? '') }}', {{ $ded->amount }}, {{ $ded->is_recurring ? 1 : 0 }}, {{ $ded->is_active ? 1 : 0 }})">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('hr.payroll.config.deductions.destroy', $ded) }}" class="d-inline"
                                                          onsubmit="return confirm('Remove this deduction?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    @if(!$hasDeds)
                                    <tr><td colspan="7" class="text-center text-muted py-4">No custom deductions configured yet.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 4: Salary Advance Repayments ────────────────────────── --}}
    <div class="tab-pane fade" id="tab-advances">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">Approved Salary Advances (Auto-Deducted on Payroll Generation)</h6>
                <a href="{{ route('hr.salary-advances.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Manage Advances
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Amount Approved</th>
                                <th>Repayment Period</th>
                                <th>Monthly Deduction</th>
                                <th>Start From</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advances as $adv)
                            @php
                                $months = max(1, (int) $adv->repayment_months);
                                $monthly = round((float) $adv->amount_approved / $months, 2);
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ optional(optional($adv->employee)->user)->name ?? '—' }}</td>
                                <td>{{ formatCurrency($adv->amount_approved) }}</td>
                                <td>{{ $months }} month(s)</td>
                                <td class="fw-bold text-danger">{{ formatCurrency($monthly) }}/month</td>
                                <td>{{ $adv->repayment_start_date ? $adv->repayment_start_date->format('M Y') : '—' }}</td>
                                <td><span class="badge bg-success">Approved</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No approved salary advances pending repayment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle me-2"></i>
            Advance repayments are automatically calculated and deducted when payroll is generated. Each approved advance is divided equally across its repayment months. Mark an advance as <strong>Paid</strong> on the Salary Advances page to stop deductions.
        </div>
    </div>

    {{-- ── TAB 5: Bank Accounts ──────────────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-banks">
        @php
            $withBank    = $employees->filter(fn($e) => $e->bank_account);
            $missingBank = $employees->reject(fn($e) => $e->bank_account);
        @endphp

        {{-- Summary strip --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-success">
                    <div class="fw-bold fs-4 text-success">{{ $withBank->count() }}</div>
                    <small class="text-muted">Accounts Configured</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-danger">
                    <div class="fw-bold fs-4 text-danger">{{ $missingBank->count() }}</div>
                    <small class="text-muted">Missing Bank Details</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center p-3 border-start border-4 border-primary">
                    <div class="fw-bold fs-4 text-primary">{{ $employees->count() }}</div>
                    <small class="text-muted">Total Active Employees</small>
                </div>
            </div>
        </div>

        @if($missingBank->count())
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>{{ $missingBank->count() }} employee(s) have no bank account on record.</strong>
                These will appear as warnings on the Salary Schedule report. Use the edit buttons below to add their details.
            </div>
        </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bank me-2 text-primary"></i>Employee Bank Account Details</h6>
                <small class="text-muted">Used in the Salary Payment Schedule report</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee Name</th>
                                <th>Emp. ID</th>
                                <th>Department</th>
                                <th>Bank Name</th>
                                <th>Account Number</th>
                                <th>Sort Code</th>
                                <th>Branch</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees->sortBy(fn($e) => optional($e->user)->name) as $emp)
                            @php $hasBankInfo = $emp->bank_name || $emp->bank_account; @endphp
                            <tr class="{{ !$hasBankInfo ? 'table-warning bg-opacity-25' : '' }}">
                                <td class="fw-semibold">{{ optional($emp->user)->name ?? '—' }}</td>
                                <td><code class="small">{{ $emp->employee_id }}</code></td>
                                <td><small>{{ optional($emp->department)->name ?? '—' }}</small></td>
                                <td>
                                    @if($emp->bank_name)
                                        {{ $emp->bank_name }}
                                    @else
                                        <span class="text-muted fst-italic small">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($emp->bank_account)
                                        <code>{{ $emp->bank_account }}</code>
                                    @else
                                        <span class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($emp->sort_code)
                                        <code>{{ $emp->sort_code }}</code>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($emp->bank_branch)
                                        {{ $emp->bank_branch }}
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($emp->bank_account)
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Complete</span>
                                    @elseif($emp->bank_name)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation me-1"></i>Partial</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Missing</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="openBankEdit(
                                            {{ $emp->id }},
                                            '{{ addslashes(optional($emp->user)->name ?? '') }}',
                                            '{{ addslashes($emp->bank_name ?? '') }}',
                                            '{{ addslashes($emp->bank_account ?? '') }}',
                                            '{{ addslashes($emp->sort_code ?? '') }}',
                                            '{{ addslashes($emp->bank_branch ?? '') }}'
                                        )">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">No active employees found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB 6: Others (Manage Types) ──────────────────────────────── --}}
    <div class="tab-pane fade" id="tab-types">

        {{-- Payroll Date --}}
        @php $payrollDateCfg = $configs->flatten()->firstWhere('key', 'payroll_date'); @endphp
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-calendar-event me-2 text-primary"></i>Monthly Payroll Date</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('hr.payroll.config.global') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Day of Month Payroll is Processed <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="configs[payroll_date]"
                                   class="form-control"
                                   value="{{ $payrollDateCfg->value ?? 25 }}"
                                   min="1" max="31" required>
                            <span class="input-group-text">of each month</span>
                        </div>
                        <div class="form-text">{{ $payrollDateCfg->description ?? '' }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">

            {{-- Allowance Types --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-plus-circle text-primary me-2"></i>Allowance Types</h6>
                    </div>
                    <div class="card-body border-bottom">
                        <form method="POST" action="{{ route('hr.payroll.config.item-types.store') }}" class="row g-2 align-items-end">
                            @csrf
                            <input type="hidden" name="category" value="allowance">
                            <div class="col">
                                <label class="form-label small mb-1">New Allowance Type Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Shift Allowance" required maxlength="100">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Add</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Name</th><th>Slug</th><th>Active</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                @php $allowAll = $allItemTypes->where('category','allowance'); @endphp
                                @forelse($allowAll as $type)
                                <tr>
                                    <td class="fw-semibold">{{ $type->name }}</td>
                                    <td><code class="small">{{ $type->slug }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $type->is_active ? 'success' : 'secondary' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="openEditType({{ $type->id }}, '{{ addslashes($type->name) }}', {{ $type->is_active ? 1 : 0 }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('hr.payroll.config.item-types.destroy', $type) }}" class="d-inline"
                                              onsubmit="return confirm('Delete this allowance type?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No allowance types yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Deduction Types --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold"><i class="bi bi-dash-circle text-danger me-2"></i>Deduction Types</h6>
                    </div>
                    <div class="card-body border-bottom">
                        <form method="POST" action="{{ route('hr.payroll.config.item-types.store') }}" class="row g-2 align-items-end">
                            @csrf
                            <input type="hidden" name="category" value="deduction">
                            <div class="col">
                                <label class="form-label small mb-1">New Deduction Type Name</label>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Insurance Premium" required maxlength="100">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-plus me-1"></i>Add</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Name</th><th>Slug</th><th>Active</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                @php $deductAll = $allItemTypes->where('category','deduction'); @endphp
                                @forelse($deductAll as $type)
                                <tr>
                                    <td class="fw-semibold">{{ $type->name }}</td>
                                    <td><code class="small">{{ $type->slug }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $type->is_active ? 'success' : 'secondary' }}">
                                            {{ $type->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary me-1"
                                            onclick="openEditType({{ $type->id }}, '{{ addslashes($type->name) }}', {{ $type->is_active ? 1 : 0 }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="{{ route('hr.payroll.config.item-types.destroy', $type) }}" class="d-inline"
                                              onsubmit="return confirm('Delete this deduction type?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No deduction types yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>{{-- /row --}}
        <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle me-2"></i>
            The <strong>slug</strong> is auto-generated from the name and is used internally. Inactive types are hidden from the allowance/deduction selection dropdowns but existing records are not affected.
        </div>
    </div>

</div>{{-- /tab-content --}}

{{-- Edit Bank Account Modal --}}
<div class="modal fade" id="editBankModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editBankForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-bank me-2"></i>Edit Bank Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Updating details for: <strong id="eb_emp_name"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" id="eb_bank_name"
                               class="form-control" maxlength="100"
                               placeholder="e.g. Zanaco, Stanbic, FNB Zambia">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text" name="bank_account" id="eb_bank_account"
                               class="form-control" maxlength="50"
                               placeholder="e.g. 0123456789">
                        <div class="form-text">This number will appear on the Salary Schedule report.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Code</label>
                        <input type="text" name="sort_code" id="eb_sort_code"
                               class="form-control" maxlength="20"
                               placeholder="e.g. 01-02-03">
                        <div class="form-text">Bank branch sort code / routing number.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bank Branch</label>
                        <input type="text" name="bank_branch" id="eb_bank_branch"
                               class="form-control" maxlength="100"
                               placeholder="e.g. Cairo Road Branch">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Bank Details
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Item Type Modal --}}
<div class="modal fade" id="editTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editTypeForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="et_name" class="form-control" required maxlength="100">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="et_active">
                        <label class="form-check-label" for="et_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Allowance Modal --}}
<div class="modal fade" id="editAllowanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editAllowanceForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Allowance</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Allowance Type</label>
                        <select name="allowance_type" id="ea_type" class="form-select" required>
                            @foreach($allowanceTypes as $t)
                            <option value="{{ $t->slug }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" id="ea_desc" class="form-control">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <div class="input-group">
                                <input type="number" name="percentage" id="ea_pct" class="form-control" placeholder="0.00" min="0" max="100" step="0.01">
                                <span class="input-group-text">% of basic</span>
                            </div>
                        </div>
                        <div class="col-auto d-flex align-items-center text-muted">or</div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="amount" id="ea_amount" class="form-control" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ea_active">
                        <label class="form-check-label" for="ea_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Deduction Modal --}}
<div class="modal fade" id="editDeductionModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editDeductionForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Deduction</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deduction Type</label>
                        <select name="deduction_type" id="ed_type" class="form-select" required>
                            @foreach($deductionTypes as $t)
                            <option value="{{ $t->slug }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" id="ed_desc" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount (K) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="ed_amount" class="form-control" required min="0.01" step="0.01">
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="is_recurring" value="1" id="ed_recurring">
                        <label class="form-check-label" for="ed_recurring">Recurring</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ed_active">
                        <label class="form-check-label" for="ed_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditAllowance(id, empId, type, desc, pct, amount, active) {
    document.getElementById('editAllowanceForm').action = '/hr/payroll/config/allowances/' + id;
    document.getElementById('ea_type').value            = type;
    document.getElementById('ea_desc').value            = desc;
    document.getElementById('ea_pct').value             = pct > 0 ? pct : '';
    document.getElementById('ea_amount').value          = pct > 0 ? '' : amount;
    document.getElementById('ea_active').checked        = active == 1;
    new bootstrap.Modal(document.getElementById('editAllowanceModal')).show();
}

function openEditType(id, name, active) {
    document.getElementById('editTypeForm').action = '/hr/payroll/config/item-types/' + id;
    document.getElementById('et_name').value       = name;
    document.getElementById('et_active').checked   = active == 1;
    new bootstrap.Modal(document.getElementById('editTypeModal')).show();
}

function openEditDeduction(id, type, desc, amount, recurring, active) {
    document.getElementById('editDeductionForm').action = '/hr/payroll/config/deductions/' + id;
    document.getElementById('ed_type').value            = type;
    document.getElementById('ed_desc').value            = desc;
    document.getElementById('ed_amount').value          = amount;
    document.getElementById('ed_recurring').checked     = recurring == 1;
    document.getElementById('ed_active').checked        = active == 1;
    new bootstrap.Modal(document.getElementById('editDeductionModal')).show();
}

function openBankEdit(id, empName, bankName, bankAccount, sortCode, bankBranch) {
    document.getElementById('editBankForm').action    = '/hr/payroll/config/bank-accounts/' + id;
    document.getElementById('eb_emp_name').textContent = empName;
    document.getElementById('eb_bank_name').value     = bankName;
    document.getElementById('eb_bank_account').value  = bankAccount;
    document.getElementById('eb_sort_code').value     = sortCode || '';
    document.getElementById('eb_bank_branch').value   = bankBranch || '';
    new bootstrap.Modal(document.getElementById('editBankModal')).show();
}

// Restore active tab from URL hash
const hash = window.location.hash;
if (hash) {
    const tab = document.querySelector('a[href="' + hash + '"]');
    if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
}
document.querySelectorAll('#configTabs a[data-bs-toggle="tab"]').forEach(el => {
    el.addEventListener('shown.bs.tab', e => {
        history.replaceState(null, '', e.target.getAttribute('href'));
    });
});
</script>
@endpush

@endsection
