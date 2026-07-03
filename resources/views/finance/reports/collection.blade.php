@extends('layouts.app')
@section('title', 'Collection Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Revenue Collection Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.reports.index') }}">Finance Reports</a></li>
            <li class="breadcrumb-item active">Collection</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('finance.reports.collection') }}" class="d-flex gap-2">
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
            <div class="col-md-2">
                <select name="payment_method" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Methods</option>
                    @foreach(['Airtel Money','MTN','Zamtel','Visa','Mastercard','Cash','Bank Transfer'] as $m)
                    <option value="{{ $m }}" {{ request('payment_method') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
                <button class="d-none" type="submit">Go</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4 text-success">K {{ number_format($totals['amount'], 2) }}</div>
            <small class="text-muted">Total Collected</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4 text-primary">{{ $totals['count'] }}</div>
            <small class="text-muted">Total Transactions</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4 text-info">K {{ number_format($totals['average'], 2) }}</div>
            <small class="text-muted">Average Payment</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-4">{{ $totals['students'] }}</div>
            <small class="text-muted">Students Paid</small>
        </div>
    </div>
</div>

<!-- Collection by Method -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">By Payment Method</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Method</th><th class="text-end">Transactions</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                        @foreach($byMethod as $method)
                        <tr>
                            <td>{{ $method->payment_method }}</td>
                            <td class="text-end">{{ $method->count }}</td>
                            <td class="text-end fw-semibold">K {{ number_format($method->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Collection by Method Chart</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="methodChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-3">
        <h6 class="mb-0 fw-semibold">Payment Transactions</h6>
    </div>
    <div class="card-body">
        <table class="table datatable table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Program</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                @php $methodColors = ['Airtel Money'=>'danger','MTN'=>'warning','Zamtel'=>'success','Visa'=>'primary','Mastercard'=>'dark','Cash'=>'secondary','Bank Transfer'=>'info'] @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                    <td><code>{{ $payment->reference_number }}</code></td>
                    <td>
                        <div class="fw-semibold" style="font-size:0.83rem">{{ $payment->studentBill?->student?->student_id ?? '—' }}</div>
                        <small class="text-muted">{{ $payment->studentBill?->student?->user?->name ?? '—' }}</small>
                    </td>
                    <td><small>{{ $payment->studentBill?->student?->program?->code ?? '—' }}</small></td>
                    <td><span class="badge bg-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">{{ $payment->payment_method }}</span></td>
                    <td class="fw-semibold">K {{ number_format($payment->amount, 2) }}</td>
                    <td><span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'reversed' ? 'danger' : 'warning') }}">{{ ucfirst($payment->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $payments->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodData = @json($byMethod ?? []);
    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: methodData.map(m => m.payment_method),
            datasets: [{
                data: methodData.map(m => m.total),
                backgroundColor: ['#dc3545','#ffc107','#198754','#0d6efd','#212529','#6c757d','#0dcaf0']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
    });
});
</script>
@endpush
