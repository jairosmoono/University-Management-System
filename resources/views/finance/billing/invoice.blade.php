<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 9pt; color: #1a1a1a; padding: 15mm; }

.header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2pt solid #1a3a5c; padding-bottom: 5mm; margin-bottom: 6mm; }
.uni-name { font-size: 15pt; font-weight: 900; color: #1a3a5c; }
.uni-sub  { font-size: 8pt; color: #666; margin-top: 1mm; }
.invoice-meta { text-align: right; }
.invoice-title { font-size: 14pt; font-weight: 700; color: #1a3a5c; text-transform: uppercase; letter-spacing: 1pt; }
.invoice-no { font-size: 9pt; color: #555; margin-top: 1mm; }

.info-row { display: flex; gap: 10mm; margin-bottom: 6mm; }
.info-box { flex: 1; background: #f5f7fa; border-left: 3pt solid #1a3a5c; padding: 3mm 4mm; }
.info-box h6 { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.5pt; color: #888; margin-bottom: 1.5mm; }
.info-box p { font-size: 8.5pt; line-height: 1.6; }

table { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
thead th { background: #1a3a5c; color: #fff; padding: 2.5mm 3mm; text-align: left; font-size: 8pt; font-weight: 600; }
tbody td { padding: 2.5mm 3mm; border-bottom: 0.5pt solid #e0e0e0; font-size: 8.5pt; vertical-align: middle; }
tbody tr:nth-child(even) { background: #fafafa; }
.amount-col { text-align: right; font-family: monospace; }

.totals { margin-left: auto; width: 70mm; margin-bottom: 6mm; }
.totals table { margin: 0; }
.totals td { padding: 1.5mm 3mm; font-size: 8.5pt; border: none; }
.totals .label { color: #555; }
.totals .total-row td { font-weight: 700; font-size: 10pt; color: #1a3a5c; border-top: 1pt solid #1a3a5c; padding-top: 2mm; }

.payments-section h6 { font-size: 8pt; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 0.5pt; margin-bottom: 2mm; }

.status-badge { display: inline-block; padding: 1mm 3mm; border-radius: 2mm; font-size: 8pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5pt; }
.status-paid    { background: #d1fae5; color: #065f46; }
.status-partial { background: #fef3c7; color: #92400e; }
.status-unpaid  { background: #fee2e2; color: #991b1b; }

.footer { margin-top: 10mm; border-top: 0.5pt solid #ccc; padding-top: 4mm; display: flex; justify-content: space-between; font-size: 7pt; color: #888; }
.watermark { position: fixed; bottom: 60mm; left: 50%; transform: translateX(-50%) rotate(-30deg); font-size: 56pt; font-weight: 900; color: rgba(26,58,92,0.06); white-space: nowrap; z-index: -1; }
</style>
</head>
<body>

<div class="watermark">{{ strtoupper($bill->status) }}</div>

<div class="header">
    <div>
        <div class="uni-name">{{ strtoupper(setting('university_name', config('app.name'))) }}</div>
        <div class="uni-sub">{{ setting('university_address', '') }}{{ setting('university_city') ? ', ' . setting('university_city') : '' }}</div>
        <div class="uni-sub">{{ setting('university_email', '') }} &bull; {{ setting('university_phone', '') }}</div>
    </div>
    <div class="invoice-meta">
        <div class="invoice-title">Fee Invoice</div>
        <div class="invoice-no"># INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div class="invoice-no" style="margin-top:1mm">
            <span class="status-badge status-{{ $bill->status }}">{{ ucfirst($bill->status) }}</span>
        </div>
    </div>
</div>

<div class="info-row">
    <div class="info-box">
        <h6>Billed To</h6>
        <p>
            <strong>{{ optional($bill->student?->user)->name ?? 'N/A' }}</strong><br>
            Student ID: {{ optional($bill->student)->student_id ?? '—' }}<br>
            Program: {{ optional($bill->student?->program)->name ?? '—' }}<br>
            Faculty: {{ optional($bill->student?->program?->department?->faculty)->name ?? '—' }}
        </p>
    </div>
    <div class="info-box">
        <h6>Invoice Details</h6>
        <p>
            Academic Year: <strong>{{ optional($bill->academicYear)->name ?? '—' }}</strong><br>
            Semester/Term: {{ optional($bill->semester)->name ?? '—' }}<br>
            Due Date: {{ $bill->due_date?->format('d M Y') ?? '—' }}<br>
            Issued: {{ $bill->created_at->format('d M Y') }}
        </p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:5mm">#</th>
            <th>Fee Description</th>
            <th style="width:20mm">Fee Type</th>
            <th style="width:25mm;text-align:right">Amount (ZMW)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bill->items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->description ?? $item->fee_type }}</td>
            <td>{{ $item->fee_type }}</td>
            <td class="amount-col">{{ number_format($item->amount, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:#aaa;padding:5mm">No fee items recorded.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="totals">
    <table>
        <tr>
            <td class="label">Subtotal</td>
            <td class="amount-col">ZMW {{ number_format($bill->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Amount Paid</td>
            <td class="amount-col" style="color:#065f46">ZMW {{ number_format($bill->amount_paid, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Balance Due</td>
            <td class="amount-col" style="color:{{ $bill->balance > 0 ? '#991b1b' : '#065f46' }}">
                ZMW {{ number_format($bill->balance, 2) }}
            </td>
        </tr>
    </table>
</div>

@if($bill->payments->isNotEmpty())
<div class="payments-section">
    <h6>Payment History</h6>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Method</th>
                <th style="text-align:right">Amount (ZMW)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                <td><code>{{ $payment->reference_number ?? $payment->transaction_reference ?? '—' }}</code></td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? '')) }}</td>
                <td class="amount-col">{{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    <div>This invoice was generated by the University Finance System on {{ now()->format('d M Y, H:i') }}.</div>
    <div>Ref: INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }} &bull; {{ optional($bill->student)->student_id }}</div>
</div>

</body>
</html>
