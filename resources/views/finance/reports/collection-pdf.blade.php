<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Revenue Collection Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a; background: #fff; }
    .header { background: #0B1F3A; color: #fff; padding: 12px 16px; margin-bottom: 14px; }
    .header h1 { font-size: 15px; font-weight: bold; }
    .header p  { font-size: 9px; opacity: 0.8; margin-top: 2px; }
    .meta { display: flex; gap: 24px; font-size: 8px; margin-top: 6px; opacity: 0.85; }

    .summary { display: flex; gap: 10px; margin-bottom: 14px; }
    .summary-card { flex: 1; border: 1px solid #ddd; border-radius: 4px; padding: 8px 10px; text-align: center; }
    .summary-card .val { font-size: 13px; font-weight: bold; color: #0B1F3A; }
    .summary-card .lbl { font-size: 7.5px; color: #666; margin-top: 2px; }

    .section-title { font-size: 10px; font-weight: bold; color: #0B1F3A; border-bottom: 2px solid #0B1F3A;
                     padding-bottom: 3px; margin-bottom: 8px; }

    .method-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
    .method-table th { background: #0B1F3A; color: #fff; padding: 5px 7px; text-align: left; font-size: 8px; }
    .method-table td { padding: 4px 7px; border-bottom: 1px solid #eee; }
    .method-table tr:nth-child(even) td { background: #f8f9fa; }
    .method-table .right { text-align: right; }

    table.main { width: 100%; border-collapse: collapse; }
    table.main th { background: #0B1F3A; color: #fff; padding: 5px 6px; text-align: left; font-size: 8px; white-space: nowrap; }
    table.main td { padding: 4px 6px; border-bottom: 1px solid #eee; vertical-align: top; }
    table.main tr:nth-child(even) td { background: #f8f9fa; }
    table.main .right { text-align: right; }
    table.main tfoot td { font-weight: bold; background: #e8ecf0; border-top: 2px solid #0B1F3A; }

    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-danger  { background: #f8d7da; color: #721c24; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-primary { background: #cce5ff; color: #004085; }
    .badge-dark    { background: #d6d8d9; color: #1b1e21; }
    .badge-info    { background: #d1ecf1; color: #0c5460; }
    .badge-secondary { background: #e2e3e5; color: #383d41; }

    .footer { margin-top: 14px; text-align: right; font-size: 7.5px; color: #888; border-top: 1px solid #ddd; padding-top: 5px; }
    .two-col { display: flex; gap: 16px; margin-bottom: 14px; }
    .two-col > div { flex: 1; }
    code { font-family: DejaVu Sans Mono, monospace; font-size: 8px; }
</style>
</head>
<body>

<div class="header">
    <h1>Revenue Collection Report</h1>
    <div class="meta">
        <span>Period: {{ $from }} &mdash; {{ $to }}</span>
        <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
    </div>
</div>

{{-- Summary --}}
<div class="summary">
    <div class="summary-card">
        <div class="val">K {{ number_format($totals['amount'], 2) }}</div>
        <div class="lbl">Total Collected</div>
    </div>
    <div class="summary-card">
        <div class="val">{{ $totals['count'] }}</div>
        <div class="lbl">Transactions</div>
    </div>
    <div class="summary-card">
        <div class="val">K {{ number_format($totals['average'], 2) }}</div>
        <div class="lbl">Average Payment</div>
    </div>
    <div class="summary-card">
        <div class="val">{{ $totals['students'] }}</div>
        <div class="lbl">Students Paid</div>
    </div>
</div>

{{-- Payment Method Breakdown --}}
@if($byMethod->count())
<div class="section-title">Payment Method Breakdown</div>
<table class="method-table">
    <thead>
        <tr>
            <th>Method</th>
            <th class="right">Transactions</th>
            <th class="right">Amount (K)</th>
            <th class="right">% of Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($byMethod as $m)
        <tr>
            <td>{{ $m->payment_method }}</td>
            <td class="right">{{ $m->count }}</td>
            <td class="right">{{ number_format($m->total, 2) }}</td>
            <td class="right">{{ $totals['amount'] > 0 ? number_format(($m->total / $totals['amount']) * 100, 1) : '0.0' }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Transactions --}}
<div class="section-title">Payment Transactions ({{ $payments->count() }} records)</div>
<table class="main">
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Program</th>
            <th>Method</th>
            <th class="right">Amount (K)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $methodColors = [
                'Airtel Money'=>'danger','MTN'=>'warning','Zamtel'=>'success',
                'Visa'=>'primary','Mastercard'=>'dark','Cash'=>'secondary','Bank Transfer'=>'info'
            ];
        @endphp
        @forelse($payments as $payment)
        <tr>
            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
            <td><code>{{ $payment->reference_number }}</code></td>
            <td>{{ $payment->studentBill?->student?->student_id ?? '—' }}</td>
            <td>{{ $payment->studentBill?->student?->user?->name ?? '—' }}</td>
            <td>{{ $payment->studentBill?->student?->program?->code ?? '—' }}</td>
            <td><span class="badge badge-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">{{ $payment->payment_method }}</span></td>
            <td class="right">{{ number_format($payment->amount, 2) }}</td>
            <td>
                <span class="badge badge-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'reversed' ? 'danger' : 'warning') }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:12px;color:#666;">No transactions found.</td></tr>
        @endforelse
    </tbody>
    @if($payments->count())
    <tfoot>
        <tr>
            <td colspan="6">TOTAL</td>
            <td class="right">{{ number_format($payments->sum('amount'), 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="footer">Confidential &mdash; Finance Department &mdash; {{ config('app.name') }}</div>
</body>
</html>
