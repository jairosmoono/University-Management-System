@extends('layouts.app')
@section('title', 'Bill Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Bill Details</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.billing.index') }}">Bills</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @can('manage-finance')
        <a href="{{ route('finance.payments.create', $bill) }}" class="btn btn-success">
            <i class="bi bi-cash-coin me-1"></i> Add Payment
        </a>
        @endcan
        <a href="{{ route('finance.billing.invoice', $bill) }}" class="btn btn-outline-danger">
            <i class="bi bi-file-pdf me-1"></i> Download Invoice
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:var(--primary)">
                    <i class="bi bi-person-fill text-white fs-2"></i>
                </div>
                <h5 class="mb-1">{{ optional(optional($bill->student)->user)->name }}</h5>
                <code class="text-muted">{{ optional($bill->student)->student_id }}</code>
                <p class="text-muted small mt-2">{{ optional(optional($bill->student)->program)->name }}</p>
                <hr>
                <div class="text-start">
                    <p class="mb-1"><strong>Academic Year:</strong> {{ optional($bill->academicYear)->name }}</p>
                    <p class="mb-1"><strong>Semester/Term:</strong> {{ optional($bill->semester)->name }}</p>
                    <p class="mb-1"><strong>Due Date:</strong> {{ optional($bill->due_date)?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Financial Summary</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Amount</span>
                    <strong>{{ formatCurrency($bill->total_amount) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Amount Paid</span>
                    <strong>{{ formatCurrency($bill->amount_paid) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between text-danger fw-bold">
                    <span>Outstanding Balance</span>
                    <strong>{{ formatCurrency($bill->balance) }}</strong>
                </div>
                <div class="mt-3">
                    @php $sc = ['paid'=>'success','partial'=>'warning','unpaid'=>'danger'] @endphp
                    <span class="badge bg-{{ $sc[$bill->status] ?? 'secondary' }} fs-6 w-100 py-2">{{ strtoupper($bill->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-list-check me-2 text-primary"></i>Bill Items</h6>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Fee Type</th><th>Description</th><th class="text-end">Amount</th><th class="text-end">Discount</th><th class="text-end">Net</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->items as $item)
                        <tr>
                            <td>{{ $item->fee_type }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-end">{{ formatCurrency($item->amount) }}</td>
                            <td class="text-end text-success">{{ formatCurrency($item->discount ?? 0) }}</td>
                            <td class="text-end fw-semibold">{{ formatCurrency($item->amount - ($item->discount ?? 0)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end">{{ formatCurrency($bill->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Payment History</h6>
            </div>
            <div class="card-body">
                @if($bill->payments->isEmpty())
                <p class="text-muted text-center py-4">No payments recorded yet.</p>
                @else
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Recorded By</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bill->payments as $payment)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                            <td class="fw-semibold text-success">{{ formatCurrency($payment->amount) }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td><code>{{ $payment->transaction_reference ?? $payment->reference_number }}</code></td>
                            <td>{{ optional($payment->recordedBy)->name ?? '—' }}</td>
                            <td>
                                @php $pc = ['verified'=>'success','pending'=>'warning','reversed'=>'danger'] @endphp
                                <span class="badge bg-{{ $pc[$payment->status] ?? 'secondary' }}">{{ ucfirst($payment->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
