<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size:10.5px;
        color:#1a1a2e;
        background:#fff;
        margin:0;
        padding:0;
    }
    .page {
        margin:12mm 16mm 10mm 16mm;
    }

    /* ── HEADER ── */
    .header { display:table; width:100%; margin-bottom:0; }
    .h-logo { display:table-cell; width:58px; vertical-align:middle; }
    .h-mid  { display:table-cell; vertical-align:middle; text-align:center; }
    .h-ref  { display:table-cell; width:58px; vertical-align:middle; text-align:right; }
    .logo-img  { width:50px; height:50px; object-fit:contain; }
    .logo-box  { width:50px; height:50px; background:#0B1F3A; border-radius:5px;
                 display:table; text-align:center; }
    .logo-box td { vertical-align:middle; color:#fff; font-size:20px; font-weight:bold; }
    .uni-name    { font-size:15px; font-weight:bold; color:#0B1F3A; }
    .uni-tagline { font-size:8px; color:#999; margin-top:2px; letter-spacing:.3px; }

    .bar-navy { height:4px; background:#0B1F3A; margin:9px 0 2px; border-radius:2px; }
    .bar-gold { height:2px; background:#c9a84c; margin-bottom:12px; border-radius:2px; }

    /* ── TITLE ROW ── */
    .title-row { display:table; width:100%; margin-bottom:12px; }
    .title-left  { display:table-cell; vertical-align:bottom; }
    .title-right { display:table-cell; vertical-align:bottom; text-align:right; width:120px; }
    .doc-title   { font-size:17px; font-weight:bold; color:#0B1F3A; text-transform:uppercase; letter-spacing:.5px; }
    .doc-sub     { font-size:8px; color:#aaa; letter-spacing:.4px; margin-top:2px; }
    .ref-lbl     { font-size:7.5px; color:#bbb; text-transform:uppercase; letter-spacing:.5px; }
    .ref-val     { font-size:12px; font-weight:bold; color:#0B1F3A; }
    .ref-date    { font-size:9px; color:#888; margin-top:1px; }

    /* ── HERO BAND ── */
    .hero { display:table; width:100%; border:1.5px solid #d8e2ef; border-radius:5px;
            overflow:hidden; margin-bottom:14px; }
    .hero-left  { display:table-cell; width:56%; background:#0B1F3A; padding:12px 16px; vertical-align:middle; }
    .hero-right { display:table-cell; width:44%; background:#f6f9fc; padding:12px 16px; vertical-align:middle; text-align:center; }
    .h-amt-lbl  { font-size:8px; color:#7da0be; text-transform:uppercase; letter-spacing:.6px; margin-bottom:3px; }
    .h-amt-val  { font-size:26px; font-weight:bold; color:#fff; line-height:1; }
    .h-amt-sub  { font-size:8.5px; color:#6a93b0; margin-top:5px; }
    .h-st-icon  { font-size:20px; margin-bottom:3px; }
    .h-st-text  { font-size:13px; font-weight:bold; }
    .h-st-sub   { font-size:8.5px; color:#888; margin-top:3px; line-height:1.5; }
    .st-verified .h-st-text { color:#16a34a; }
    .st-pending  .h-st-text { color:#d97706; }
    .st-reversed .h-st-text { color:#dc2626; }

    /* ── SECTION LABEL ── */
    .sec-lbl {
        font-size:8px; font-weight:bold; color:#0B1F3A; text-transform:uppercase;
        letter-spacing:.7px; background:#eef2f8; padding:5px 8px;
        border-left:3px solid #0B1F3A; margin-bottom:0;
    }

    /* ── TWO COLUMNS ── */
    .two-col { display:table; width:100%; margin-bottom:14px; table-layout:fixed; }
    .tcol-l  { display:table-cell; width:48%; vertical-align:top; }
    .tcol-gap { display:table-cell; width:4%; }
    .tcol-r  { display:table-cell; width:48%; vertical-align:top; }

    /* ── INFO TABLE ── */
    table.inf {
        width:100%; border-collapse:collapse;
        border:1px solid #e4eaf2; font-size:10px;
    }
    table.inf td {
        padding:5px 8px; border-bottom:1px solid #edf1f7; vertical-align:top;
    }
    table.inf td.l { color:#888; width:42%; font-size:9.5px; }
    table.inf td.v { font-weight:600; color:#1a1a2e; }
    table.inf tr:last-child td { border-bottom:none; }
    table.inf tr:nth-child(even) td { background:#fafbfd; }

    /* ── BILL SUMMARY ── */
    .bill-wrap { border:1px solid #e4eaf2; margin-bottom:12px; }
    .bill-row { display:table; width:100%; padding:5px 8px; border-bottom:1px solid #edf1f7; }
    .bill-row:last-child { border-bottom:none; }
    .bl { display:table-cell; font-size:10px; color:#555; }
    .bv { display:table-cell; text-align:right; font-size:10px; font-weight:bold; color:#1a1a2e; }
    .bill-row.hi  td, .bill-row.hi .bl, .bill-row.hi .bv { background:#f0f7ee; }
    .bill-row.hi  .bv { color:#16a34a; }
    .bill-row.tot { background:#0B1F3A; }
    .bill-row.tot .bl, .bill-row.tot .bv { color:#fff; font-size:11px; }

    /* ── NOTES ── */
    .notes { background:#fffdf0; border:1px solid #fde68a; border-left:3px solid #c9a84c;
             border-radius:3px; padding:6px 9px; font-size:9.5px; margin-bottom:12px; color:#78350f; }

    /* ── SIGNATURES ── */
    hr.dash { border:none; border-top:1px dashed #ccd4e0; margin:10px 0 10px; }
    .sig-row  { display:table; width:100%; }
    .sig-cell { display:table-cell; text-align:center; width:50%; padding:0 22px; }
    .sig-line { border-top:1px solid #bbb; margin-bottom:4px; }
    .sig-lbl  { font-size:8px; color:#aaa; text-transform:uppercase; letter-spacing:.3px; }

    /* ── FOOTER ── */
    .footer {
        margin-top:12px;
        background:#0B1F3A;
        color:#7da0be;
        text-align:center;
        font-size:7.5px;
        padding:7px 10px;
        letter-spacing:.3px;
        border-radius:3px;
    }
    .footer span { color:#9db3cc; }
</style>
</head>
<body>

<div class="page">
    {{-- HEADER --}}
    <div class="header">
        <div class="h-logo">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="logo-img" alt="">
            @else
                <table class="logo-box"><tr><td>{{ strtoupper(substr($uniName,0,1)) }}</td></tr></table>
            @endif
        </div>
        <div class="h-mid">
            <div class="uni-name">{{ $uniName }}</div>
            <div class="uni-tagline">Finance Department &mdash; Official Payment Receipt</div>
        </div>
        <div class="h-ref"></div>
    </div>

    <div class="bar-navy"></div>
    <div class="bar-gold"></div>

    {{-- TITLE + REF --}}
    <div class="title-row">
        <div class="title-left">
            <div class="doc-title">Payment Receipt</div>
            <div class="doc-sub">Official Finance Document &bull; Keep for your records</div>
        </div>
        <div class="title-right">
            <div class="ref-lbl">Receipt No.</div>
            <div class="ref-val">{{ $payment->reference_number }}</div>
            <div class="ref-date">{{ $payment->payment_date->format('d F Y') }}</div>
        </div>
    </div>

    {{-- HERO --}}
    <div class="hero">
        <div class="hero-left">
            <div class="h-amt-lbl">Amount Paid</div>
            <div class="h-amt-val">ZMW {{ number_format($payment->amount, 2) }}</div>
            <div class="h-amt-sub">
                {{ $payment->payment_method }}
                @if($payment->transaction_reference)
                    &nbsp;&bull;&nbsp;{{ $payment->transaction_reference }}
                @endif
            </div>
        </div>
        <div class="hero-right st-{{ $payment->status }}">
            @if($payment->status === 'verified')
                <div class="h-st-icon" style="color:#16a34a">&#10003;</div>
            @elseif($payment->status === 'pending')
                <div class="h-st-icon" style="color:#d97706">&#9679;</div>
            @else
                <div class="h-st-icon" style="color:#dc2626">&#10007;</div>
            @endif
            <div class="h-st-text">{{ ucfirst($payment->status) }}</div>
            <div class="h-st-sub">
                Recorded by {{ optional($payment->recordedBy)->name ?? '—' }}<br>
                {{ $payment->payment_date->format('d M Y') }}
            </div>
        </div>
    </div>

    {{-- PAYMENT DETAILS + STUDENT INFO --}}
    <div class="two-col">
        <div class="tcol-l">
            <div class="sec-lbl">Payment Details</div>
            <table class="inf">
                <tr><td class="l">Payment Method</td><td class="v">{{ $payment->payment_method }}</td></tr>
                <tr><td class="l">Transaction Ref.</td><td class="v">{{ $payment->transaction_reference ?? '—' }}</td></tr>
                <tr><td class="l">Payment Date</td><td class="v">{{ $payment->payment_date->format('d M Y') }}</td></tr>
                <tr><td class="l">Recorded By</td><td class="v">{{ optional($payment->recordedBy)->name ?? '—' }}</td></tr>
            </table>
        </div>
        <div class="tcol-gap"></div>
        <div class="tcol-r">
            <div class="sec-lbl">Student Information</div>
            <table class="inf">
                <tr><td class="l">Full Name</td><td class="v">{{ optional(optional($payment->studentBill?->student)?->user)->name ?? '—' }}</td></tr>
                <tr><td class="l">Student ID</td><td class="v">{{ optional($payment->studentBill?->student)->student_id ?? '—' }}</td></tr>
                <tr><td class="l">Programme</td><td class="v">{{ optional(optional($payment->studentBill?->student)?->program)->name ?? '—' }}</td></tr>
                <tr><td class="l">Semester/Term</td><td class="v">{{ optional($payment->studentBill?->semester)->name ?? '—' }}</td></tr>
            </table>
        </div>
    </div>

    {{-- BILL SUMMARY --}}
    @if($payment->studentBill)
    @php $bill = $payment->studentBill; $prev = ($bill->amount_paid ?? 0) - $payment->amount; @endphp
    <div class="sec-lbl">Bill Summary</div>
    <div class="bill-wrap">
        <div class="bill-row">
            <div class="bl">Total Bill Amount</div>
            <div class="bv">ZMW {{ number_format($bill->total_amount ?? 0, 2) }}</div>
        </div>
        <div class="bill-row">
            <div class="bl">Previously Paid</div>
            <div class="bv">ZMW {{ number_format(max($prev, 0), 2) }}</div>
        </div>
        <div class="bill-row hi">
            <div class="bl">This Payment</div>
            <div class="bv">ZMW {{ number_format($payment->amount, 2) }}</div>
        </div>
        <div class="bill-row">
            <div class="bl">Total Paid to Date</div>
            <div class="bv">ZMW {{ number_format($bill->amount_paid ?? 0, 2) }}</div>
        </div>
        <div class="bill-row tot">
            <div class="bl">Outstanding Balance</div>
            <div class="bv">ZMW {{ number_format($bill->balance ?? 0, 2) }}</div>
        </div>
    </div>
    @endif

    @if($payment->notes)
    <div class="notes"><strong>Notes:</strong> {{ $payment->notes }}</div>
    @endif

    {{-- SIGNATURES --}}
    <hr class="dash">
    <div class="sig-row">

        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-lbl">Finance Officer / Cashier</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-lbl">Student / Authorized Payer</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Generated {{ now()->format('d M Y, H:i') }}
        &nbsp;&bull;&nbsp; {{ $uniName }}
        &nbsp;&bull;&nbsp; <span>This is an official receipt. Please retain for your records.</span>
        &nbsp;&bull;&nbsp; Ref: {{ $payment->reference_number }}
    </div>

</div>{{-- /.page --}}
</body>
</html>
