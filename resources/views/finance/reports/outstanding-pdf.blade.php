<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Outstanding Balances Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a; background: #fff; }
    .header { background: #8B0000; color: #fff; padding: 12px 16px; margin-bottom: 14px; }
    .header h1 { font-size: 15px; font-weight: bold; }
    .header p  { font-size: 9px; opacity: 0.8; margin-top: 2px; }
    .meta { display: flex; gap: 24px; font-size: 8px; margin-top: 6px; opacity: 0.85; }

    .summary { display: flex; gap: 10px; margin-bottom: 14px; }
    .summary-card { flex: 1; border: 1px solid #ddd; border-radius: 4px; padding: 8px 10px; text-align: center; }
    .summary-card.red   { border-left: 4px solid #dc3545; }
    .summary-card.amber { border-left: 4px solid #fd7e14; }
    .summary-card.blue  { border-left: 4px solid #0dcaf0; }
    .summary-card .val  { font-size: 13px; font-weight: bold; color: #8B0000; }
    .summary-card .lbl  { font-size: 7.5px; color: #666; margin-top: 2px; }

    .section-title { font-size: 10px; font-weight: bold; color: #8B0000; border-bottom: 2px solid #8B0000;
                     padding-bottom: 3px; margin-bottom: 8px; }

    .prog-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
    .prog-table th { background: #8B0000; color: #fff; padding: 5px 7px; text-align: left; font-size: 8px; }
    .prog-table td { padding: 4px 7px; border-bottom: 1px solid #eee; }
    .prog-table tr:nth-child(even) td { background: #fdf6f6; }
    .prog-table .right { text-align: right; }

    table.main { width: 100%; border-collapse: collapse; }
    table.main th { background: #8B0000; color: #fff; padding: 5px 6px; text-align: left; font-size: 8px; white-space: nowrap; }
    table.main td { padding: 4px 6px; border-bottom: 1px solid #eee; vertical-align: top; }
    table.main .unpaid td { background: #fff0f0; }
    table.main .partial td { background: #fffbf0; }
    table.main .right { text-align: right; }
    table.main tfoot td { font-weight: bold; background: #f0e8e8; border-top: 2px solid #8B0000; }

    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 7px; font-weight: bold; }
    .badge-danger  { background: #f8d7da; color: #721c24; }
    .badge-warning { background: #fff3cd; color: #856404; }

    .overdue { color: #dc3545; font-weight: bold; }
    .footer { margin-top: 14px; text-align: right; font-size: 7.5px; color: #888; border-top: 1px solid #ddd; padding-top: 5px; }
</style>
</head>
<body>

<div class="header">
    <h1>Outstanding Balances Report</h1>
    <div class="meta">
        <span>Generated: {{ now()->format('d M Y, H:i') }}</span>
        <span>Total Records: {{ $bills->count() }}</span>
    </div>
</div>

{{-- Summary --}}
<div class="summary">
    <div class="summary-card red">
        <div class="val">K {{ number_format($totals['total_outstanding'] ?? 0, 2) }}</div>
        <div class="lbl">Total Outstanding</div>
    </div>
    <div class="summary-card amber">
        <div class="val">{{ $totals['unpaid_count'] ?? 0 }}</div>
        <div class="lbl">Fully Unpaid</div>
    </div>
    <div class="summary-card blue">
        <div class="val">{{ $totals['partial_count'] ?? 0 }}</div>
        <div class="lbl">Partial Payers</div>
    </div>
</div>

{{-- By Program --}}
@if($byProgram->count())
<div class="section-title">Breakdown by Program</div>
<table class="prog-table">
    <thead>
        <tr>
            <th>Program</th>
            <th class="right">Students</th>
            <th class="right">Outstanding (K)</th>
            <th class="right">% of Total</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = $totals['total_outstanding'] ?? 1; if ($grandTotal == 0) $grandTotal = 1; @endphp
        @foreach($byProgram as $progName => $data)
        <tr>
            <td>{{ $progName }}</td>
            <td class="right">{{ $data['count'] }}</td>
            <td class="right">{{ number_format($data['total'], 2) }}</td>
            <td class="right">{{ number_format(($data['total'] / $grandTotal) * 100, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- Bills Table --}}
<div class="section-title">Students with Outstanding Balances</div>
<table class="main">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Program</th>
            <th>Semester/Term</th>
            <th class="right">Billed (K)</th>
            <th class="right">Paid (K)</th>
            <th class="right">Balance (K)</th>
            <th>Status</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bills as $bill)
        @php $isOverdue = $bill->due_date && \Carbon\Carbon::parse($bill->due_date)->isPast(); @endphp
        <tr class="{{ $bill->status === 'unpaid' ? 'unpaid' : 'partial' }}">
            <td>{{ $bill->student->student_id ?? '—' }}</td>
            <td>{{ $bill->student->user->name ?? '—' }}</td>
            <td>{{ optional($bill->student?->program)->code ?? '—' }}</td>
            <td>{{ optional($bill->semester)->name ?? '—' }}</td>
            <td class="right">{{ number_format($bill->total_amount, 2) }}</td>
            <td class="right">{{ number_format($bill->amount_paid, 2) }}</td>
            <td class="right" style="font-weight:bold;color:#8B0000;">{{ number_format($bill->balance, 2) }}</td>
            <td>
                <span class="badge badge-{{ $bill->status === 'unpaid' ? 'danger' : 'warning' }}">
                    {{ ucfirst($bill->status) }}
                </span>
            </td>
            <td class="{{ $isOverdue ? 'overdue' : '' }}">
                {{ $bill->due_date ? \Carbon\Carbon::parse($bill->due_date)->format('d M Y') : '—' }}
                @if($isOverdue) (OD) @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;padding:12px;color:#666;">No outstanding balances found.</td></tr>
        @endforelse
    </tbody>
    @if($bills->count())
    <tfoot>
        <tr>
            <td colspan="4">TOTAL ({{ $bills->count() }} records)</td>
            <td class="right">{{ number_format($bills->sum('total_amount'), 2) }}</td>
            <td class="right">{{ number_format($bills->sum('amount_paid'), 2) }}</td>
            <td class="right" style="color:#8B0000;">{{ number_format($bills->sum('balance'), 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="footer">Confidential &mdash; Finance Department &mdash; {{ config('app.name') }}</div>
</body>
</html>
