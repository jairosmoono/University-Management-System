@extends('layouts.app')
@section('title', $isSelfView ? 'My Bills' : 'Bills — ' . $student->full_name)
@section('page-title', $isSelfView ? 'My Bills' : 'Student Bills')

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-receipt-cutoff me-2 text-primary"></i>
            {{ $isSelfView ? 'My Bills & Fees' : $student->full_name . ' — Bills' }}
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if(!$isSelfView)
            <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Billing</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $isSelfView ? 'My Bills' : $student->full_name }}</li>
        </ol></nav>
        @if(!$isSelfView)
        <small class="text-muted">{{ $student->student_id }} &bull; {{ $student->program?->name }}</small>
        @endif
    </div>
    @if(!$isSelfView)
    <a href="{{ route('finance.billing.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
    @endif
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ formatCurrency($stats['total_billed']) }}</div>
                    <div class="stat-label text-muted">Total Billed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-success">{{ formatCurrency($stats['total_paid']) }}</div>
                    <div class="stat-label text-muted">Total Paid</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-danger">{{ formatCurrency($stats['outstanding']) }}</div>
                    <div class="stat-label text-muted">Outstanding</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                @php $billCount = $bills->count(); @endphp
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <div class="stat-value text-warning">{{ $billCount }}</div>
                    <div class="stat-label text-muted">
                        {{ $billCount === 1 ? 'Bill' : 'Bills' }}
                        @if($stats['unpaid_count'] > 0)
                            <span class="text-danger fw-normal">/ {{ $stats['unpaid_count'] }} unpaid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── OUTSTANDING ALERT ──────────────────────────────────────────────────── --}}
@if($stats['outstanding'] > 0)
<div class="alert alert-warning d-flex align-items-center gap-3 mb-4" role="alert">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div>
        You have an outstanding balance of <strong>{{ formatCurrency($stats['outstanding']) }}</strong>.
        Please settle your fees to avoid academic restrictions.
    </div>
</div>
@endif

{{-- ── BILLS LIST ──────────────────────────────────────────────────────────── --}}
@forelse($bills as $bill)
@php
    $sc       = ['paid' => 'success', 'partial' => 'warning', 'unpaid' => 'danger'];
    $pct      = $bill->total_amount > 0 ? round(($bill->amount_paid / $bill->total_amount) * 100) : 0;
    $barColor = $bill->status === 'paid' ? 'success' : ($bill->status === 'partial' ? 'warning' : 'danger');
    $billId   = 'bill-' . $bill->id;
@endphp
<div class="card border-0 shadow-sm mb-3">
    {{-- Bill header --}}
    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3 flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-0">
                {{ optional($bill->semester)->name ?? optional($bill->academicYear)->name ?? 'Bill #' . $bill->id }}
            </h6>
            @if($bill->academicYear && $bill->semester)
            <small class="text-muted">{{ $bill->academicYear->name }}</small>
            @endif
            @if($bill->due_date)
            <small class="text-muted d-block">
                Due: <span class="{{ \Carbon\Carbon::parse($bill->due_date)->isPast() && $bill->status !== 'paid' ? 'text-danger fw-semibold' : '' }}">
                    {{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}
                </span>
            </small>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-{{ $sc[$bill->status] ?? 'secondary' }} px-3 py-2">
                {{ ucfirst($bill->status) }}
            </span>
            <a href="{{ route('finance.billing.invoice', $bill) }}"
               class="btn btn-sm btn-outline-secondary" title="Download Invoice">
                <i class="bi bi-file-pdf me-1"></i> Invoice
            </a>
            <a href="{{ route('finance.billing.show', $bill) }}"
               class="btn btn-sm btn-outline-primary" title="View Details">
                <i class="bi bi-eye me-1"></i> View
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Amount summary --}}
        <div class="row g-3 mb-3">
            <div class="col-4 text-center">
                <div class="text-muted small mb-1">Total Billed</div>
                <div class="fw-bold fs-6">{{ formatCurrency($bill->total_amount) }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="text-muted small mb-1">Amount Paid</div>
                <div class="fw-bold fs-6 text-success">{{ formatCurrency($bill->amount_paid) }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="text-muted small mb-1">Balance Due</div>
                <div class="fw-bold fs-6 {{ $bill->balance > 0 ? 'text-danger' : 'text-success' }}">
                    {{ formatCurrency($bill->balance) }}
                </div>
            </div>
        </div>

        {{-- Payment progress --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1" style="font-size:0.75rem">
                <span class="text-muted">Payment progress</span>
                <span class="fw-semibold text-{{ $barColor }}">{{ $pct }}%</span>
            </div>
            <div class="progress" style="height:8px;border-radius:6px">
                <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
            </div>
        </div>

        {{-- Fee breakdown (collapsible) --}}
        @if($bill->items->count())
        <div>
            <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none"
                    type="button" data-bs-toggle="collapse" data-bs-target="#items-{{ $billId }}">
                <i class="bi bi-chevron-down me-1"></i>Fee Breakdown ({{ $bill->items->count() }} items)
            </button>
            <div class="collapse mt-2" id="items-{{ $billId }}">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Description</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->items as $item)
                        <tr>
                            <td style="font-size:0.875rem">{{ $item->description }}</td>
                            <td class="text-end fw-semibold" style="font-size:0.875rem">{{ formatCurrency($item->amount) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td class="fw-bold">Total</td>
                            <td class="text-end fw-bold">{{ formatCurrency($bill->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        {{-- Payment history (collapsible) --}}
        @if($bill->payments->count())
        <div class="mt-2">
            <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none"
                    type="button" data-bs-toggle="collapse" data-bs-target="#payments-{{ $billId }}">
                <i class="bi bi-chevron-down me-1"></i>Payment History ({{ $bill->payments->count() }})
            </button>
            <div class="collapse mt-2" id="payments-{{ $billId }}">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->payments as $payment)
                        <tr>
                            <td style="font-size:0.875rem">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                            <td style="font-size:0.875rem">
                                <span class="badge bg-light text-dark">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</span>
                            </td>
                            <td><code style="font-size:0.75rem">{{ $payment->reference_number ?? '—' }}</code></td>
                            <td class="text-end fw-semibold text-success" style="font-size:0.875rem">
                                {{ formatCurrency($payment->amount) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-receipt fs-2 d-block mb-2 opacity-50"></i>
        No bills found.
        @if($isSelfView)
        <div class="mt-1 small">Bills will appear here once they are generated by the finance office.</div>
        @endif
    </div>
</div>
@endforelse

@endsection
