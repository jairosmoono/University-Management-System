<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Salary Schedule</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a1a;
           padding: 16px 20px; }

    /* ── Badges ─────────────── */
    .badge { display: inline-block; padding: 1px 6px; border-radius: 2px;
             font-size: 7.5px; font-weight: bold; }
    .b-warn { background: #fff3cd; color: #856404; }

    /* ── Header ─────────────── */
    table.hdr { width: 100%; border-collapse: collapse;
                border-bottom: 3px solid #0B1F3A; margin-bottom: 12px; }
    table.hdr td { vertical-align: middle; padding-bottom: 10px; }
    .org-logo { width: 40px; height: 40px; object-fit: contain;
                vertical-align: middle; margin-right: 8px; }
    .org-init { display: inline-block; width: 40px; height: 40px;
                background: #0B1F3A; border-radius: 50%; text-align: center;
                line-height: 40px; color: #fff; font-size: 16px; font-weight: bold;
                vertical-align: middle; margin-right: 8px; }
    .org-name { font-size: 13px; font-weight: bold; color: #0B1F3A;
                vertical-align: middle; }
    .org-sub  { font-size: 8px; color: #666; }
    .doc-title { font-size: 16px; font-weight: bold; color: #0B1F3A;
                 text-transform: uppercase; letter-spacing: 0.8px; }
    .doc-meta  { font-size: 8px; color: #555; margin-top: 4px; }
    .doc-stamp { margin-top: 4px; font-size: 8px; color: #888; }

    /* ── Summary strip ───────── */
    table.sum { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.sum td { border: 1px solid #ddd; padding: 6px 8px; text-align: center;
                   border-top-width: 3px; }
    .t-blue  { border-top-color: #0d6efd; }
    .t-green { border-top-color: #198754; }
    .t-info  { border-top-color: #0dcaf0; }
    .s-val { font-size: 12px; font-weight: bold; color: #0B1F3A; }
    .s-lbl { font-size: 7px; color: #666; margin-top: 1px; }

    /* ── Notice box ──────────── */
    .notice { background: #fff8e1; border: 1px solid #ffe082; border-left: 4px solid #f59e0b;
              padding: 5px 8px; margin-bottom: 10px; font-size: 7.5px; color: #7c4c00; }

    /* ── Main table ──────────── */
    table.sched { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.sched colgroup col.c-no   { width: 5%; }
    table.sched colgroup col.c-name { width: 28%; }
    table.sched colgroup col.c-id   { width: 12%; }
    table.sched colgroup col.c-dept { width: 18%; }
    table.sched colgroup col.c-bank { width: 15%; }
    table.sched colgroup col.c-acc  { width: 14%; }
    table.sched colgroup col.c-amt  { width: 13%; }

    table.sched th { background: #0B1F3A; color: #fff; padding: 5px 6px;
                     font-size: 8px; text-align: left; }
    table.sched th.r { text-align: right; }
    table.sched td { padding: 4px 6px; border-bottom: 1px solid #eee;
                     font-size: 8.5px; vertical-align: middle;
                     overflow: hidden; word-wrap: break-word; }
    table.sched tr:nth-child(even) td { background: #f9f9f9; }
    table.sched .no-account td { background: #fff8f0; }
    table.sched tfoot td { font-weight: bold; background: #e8f5e9;
                           border-top: 2px solid #198754; padding: 5px 6px;
                           font-size: 9px; }
    .r { text-align: right; }
    .net { font-weight: bold; color: #1a5c2b; }
    .miss { color: #dc3545; font-size: 7.5px; }

    code { font-family: DejaVu Sans Mono, monospace; font-size: 8px; }

    /* ── Signatures ──────────── */
    table.sigs { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table.sigs td { font-size: 8px; color: #333; vertical-align: bottom; }
    .sig-line { border-top: 1px solid #555; margin-top: 22px; padding-top: 4px; width: 140px; }

    /* ── Footer ──────────────── */
    table.foot { width: 100%; border-collapse: collapse;
                 margin-top: 10px; border-top: 1px solid #ddd; }
    table.foot td { font-size: 7px; color: #999; padding-top: 4px; }
</style>
</head>
<body>

{{-- Header --}}
<table class="hdr">
    <tr>
        <td style="width:60%">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="org-logo" alt="">
            @else
                <span class="org-init">{{ strtoupper(substr($uniName,0,1)) }}</span>
            @endif
            <span class="org-name">{{ $uniName }}</span><br>
            <span class="org-sub" style="padding-left:48px">Human Resources / Finance Department</span>
        </td>
        <td style="text-align:right">
            <div class="doc-title">Salary Schedule</div>
            <div class="doc-meta">
                Pay Period: <strong>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</strong>
                @if($department) &bull; {{ $department->name }} @endif
            </div>
            <div class="doc-stamp">Generated: {{ now()->format('d M Y, H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Summary strip --}}
<table class="sum">
    <tr>
        <td class="t-blue">
            <div class="s-val">{{ $totalCount }}</div>
            <div class="s-lbl">Employees</div>
        </td>
        <td class="t-green">
            <div class="s-val">K {{ number_format($totalNet, 2) }}</div>
            <div class="s-lbl">Total Net Payroll</div>
        </td>
        <td class="t-info">
            <div class="s-val">K {{ $totalCount > 0 ? number_format($totalNet / $totalCount, 2) : '0.00' }}</div>
            <div class="s-lbl">Average Net Pay</div>
        </td>
    </tr>
</table>

{{-- Missing bank account notice --}}
@php $missing = $payrolls->filter(fn($p) => empty(optional($p->employee)->bank_account)); @endphp
@if($missing->count())
<div class="notice">
    <strong>Note:</strong> {{ $missing->count() }} employee(s) have no bank account on record and are highlighted below.
    Please update their details before processing the bank transfer.
</div>
@endif

{{-- Schedule table --}}
<table class="sched">
    <colgroup>
        <col style="width:4%">
        <col style="width:24%">
        <col style="width:16%">
        <col style="width:14%">
        <col style="width:15%">
        <col style="width:13%">
        <col style="width:14%">
    </colgroup>
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Bank Name</th>
            <th>Branch</th>
            <th>Account Number</th>
            <th>Sort Code</th>
            <th class="r">Net Pay (K)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payrolls as $i => $p)
        @php $hasAccount = !empty(optional($p->employee)->bank_account); @endphp
        <tr class="{{ $hasAccount ? '' : 'no-account' }}">
            <td>{{ $loop->iteration }}</td>
            <td style="font-weight:600">{{ optional(optional($p->employee)->user)->name ?? '—' }}</td>
            <td>{{ optional($p->employee)->bank_name ?: '—' }}</td>
            <td>{{ optional($p->employee)->bank_branch ?: '—' }}</td>
            <td>
                @if($hasAccount)
                    <code>{{ optional($p->employee)->bank_account }}</code>
                @else
                    <span class="miss">&#9888; Not set</span>
                @endif
            </td>
            <td><code>{{ optional($p->employee)->sort_code ?: '—' }}</code></td>
            <td class="r net">{{ number_format($p->net_pay, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" style="text-align:center;padding:14px;color:#888">
                No processed or paid payroll records for this period.
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($payrolls->count())
    <tfoot>
        <tr>
            <td colspan="6" class="r">TOTAL &mdash; {{ $totalCount }} employees</td>
            <td class="r">{{ number_format($totalNet, 2) }}</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Authorisation signatures --}}
<table class="sigs">
    <tr>
        <td style="width:33%">
            <div class="sig-line">Prepared by (HR)</div>
        </td>
        <td style="width:33%;text-align:center">
            <div class="sig-line" style="margin:22px auto 0">Verified by (Finance)</div>
        </td>
        <td style="width:33%;text-align:right">
            <div class="sig-line" style="margin:22px 0 0 auto">Authorised by (Controlling officer)</div>
        </td>
    </tr>
</table>

{{-- Footer --}}
<table class="foot">
    <tr>
        <td>Confidential &mdash; For Internal Use Only &mdash; {{ $uniName }}</td>
        <td class="r">Salary Schedule &mdash; {{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</td>
    </tr>
</table>

</body>
</html>
