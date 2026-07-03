<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payroll Report</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #1a1a1a;
           padding: 14px 18px; }

    /* ── Utility ─────────────────────────── */
    .r  { text-align: right; }
    .c  { text-align: center; }
    .b  { font-weight: bold; }
    .tc { color: #8B0000; }
    .tg { color: #155724; }

    /* ── Badges ──────────────────────────── */
    .badge { display: inline-block; padding: 1px 5px; border-radius: 2px;
             font-size: 7px; font-weight: bold; }
    .b-paid      { background: #d4edda; color: #155724; }
    .b-processed { background: #cce5ff; color: #004085; }
    .b-pending   { background: #fff3cd; color: #856404; }
    .b-all       { background: #e2e3e5; color: #383d41; }

    /* ── Header ──────────────────────────── */
    table.hdr { width: 100%; border-collapse: collapse;
                border-bottom: 3px solid #0B1F3A; margin-bottom: 10px; }
    table.hdr td { vertical-align: middle; padding-bottom: 8px; }
    .org-logo  { width: 36px; height: 36px; object-fit: contain;
                 vertical-align: middle; margin-right: 7px; }
    .org-init  { display: inline-block; width: 36px; height: 36px;
                 background: #0B1F3A; border-radius: 50%; text-align: center;
                 line-height: 36px; color: #fff; font-size: 14px; font-weight: bold;
                 vertical-align: middle; margin-right: 7px; }
    .org-name  { font-size: 12px; font-weight: bold; color: #0B1F3A;
                 vertical-align: middle; }
    .org-sub   { font-size: 7.5px; color: #666; }
    .rpt-title { font-size: 14px; font-weight: bold; color: #0B1F3A;
                 text-transform: uppercase; letter-spacing: 0.8px; }
    .rpt-meta  { font-size: 7.5px; color: #555; margin-top: 3px; }

    /* ── Summary strip ───────────────────── */
    table.sum { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.sum td { border: 1px solid #ddd; padding: 5px 6px; text-align: center;
                   border-top-width: 3px; }
    table.sum .t-blue   { border-top-color: #0d6efd; }
    table.sum .t-green  { border-top-color: #198754; }
    table.sum .t-amber  { border-top-color: #fd7e14; }
    table.sum .t-purple { border-top-color: #6f42c1; }
    table.sum .t-red    { border-top-color: #dc3545; }
    table.sum .t-dark   { border-top-color: #0B1F3A; }
    .s-val { font-size: 10px; font-weight: bold; color: #0B1F3A; line-height: 1.3; }
    .s-lbl { font-size: 6.5px; color: #666; }

    /* ── Section heading ─────────────────── */
    .sec { font-size: 8px; font-weight: bold; color: #0B1F3A;
           border-bottom: 1.5px solid #0B1F3A; padding-bottom: 2px;
           margin-bottom: 5px; margin-top: 10px; }

    /* ── Department table ────────────────── */
    table.dept { width: 100%; border-collapse: collapse; }
    table.dept th { background: #0B1F3A; color: #fff; padding: 4px 6px;
                    font-size: 7.5px; text-align: left; }
    table.dept td { padding: 3px 6px; border-bottom: 1px solid #eee; font-size: 7.5px; }
    table.dept tr:nth-child(even) td { background: #f8f9fa; }
    table.dept tfoot td { font-weight: bold; background: #e8ecf0;
                          border-top: 1.5px solid #0B1F3A; }

    /* ── Main payroll table ──────────────── */
    table.pay { width: 100%; border-collapse: collapse; table-layout: fixed; }
    table.pay th { background: #0B1F3A; color: #fff; padding: 4px 4px;
                   font-size: 7px; text-align: left;
                   overflow: hidden; white-space: nowrap; }
    table.pay td { padding: 3px 4px; border-bottom: 1px solid #eee;
                   font-size: 7.5px; vertical-align: middle;
                   overflow: hidden; word-wrap: break-word; }
    table.pay tr:nth-child(even) td { background: #f9f9f9; }
    table.pay tr.pending   td { background: #fffdf0; }
    table.pay tr.processed td { background: #f0f7ff; }
    table.pay tr.paid      td { background: #f0fff4; }
    table.pay tfoot td { font-weight: bold; background: #dce4ef;
                         border-top: 1.5px solid #0B1F3A;
                         font-size: 7.5px; padding: 3px 4px; }

    /* ── Signatures ──────────────────────── */
    table.sigs { width: 100%; border-collapse: collapse; margin-top: 16px; }
    table.sigs td { font-size: 7.5px; color: #333; vertical-align: bottom; }
    .sig-line { border-top: 1px solid #555; margin-top: 20px;
                padding-top: 3px; width: 130px; }

    /* ── Footer ──────────────────────────── */
    table.foot { width: 100%; border-collapse: collapse;
                 margin-top: 8px; border-top: 1px solid #ddd; }
    table.foot td { font-size: 6.5px; color: #999; padding-top: 4px; }

    code { font-family: DejaVu Sans Mono, monospace; font-size: 7px; }
</style>
</head>
<body>

{{-- Header --}}
<table class="hdr">
    <tr>
        <td style="width:55%">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="org-logo" alt="">
            @else
                <span class="org-init">{{ strtoupper(substr($uniName,0,1)) }}</span>
            @endif
            <span class="org-name">{{ $uniName }}</span><br>
            <span class="org-sub" style="padding-left:43px">Human Resources Department</span>
        </td>
        <td style="text-align:right">
            <div class="rpt-title">Payroll Report</div>
            <div class="rpt-meta">
                Period: <strong>{{ date('F Y', mktime(0,0,0,$month,1,$year)) }}</strong>
                @if($department) &bull; Dept: {{ $department->name }} @endif
                @if($status) &bull; Status: {{ ucfirst($status) }} @endif
            </div>
            <div style="margin-top:3px">
                @php $bc = $status==='paid' ? 'b-paid' : ($status==='processed' ? 'b-processed' : ($status==='pending' ? 'b-pending' : 'b-all')); @endphp
                <span class="badge {{ $bc }}">{{ $status ? ucfirst($status) : 'All Statuses' }}</span>
                &nbsp;<span style="font-size:7px;color:#666">Generated: {{ now()->format('d M Y, H:i') }}</span>
            </div>
        </td>
    </tr>
</table>

{{-- Summary strip --}}
<table class="sum">
    <tr>
        <td class="t-blue"><div class="s-val">{{ $summary['total_employees'] }}</div><div class="s-lbl">Employees</div></td>
        <td class="t-green"><div class="s-val">K {{ number_format($summary['total_basic'], 2) }}</div><div class="s-lbl">Total Basic</div></td>
        <td class="t-amber"><div class="s-val">K {{ number_format($summary['total_allowances'], 2) }}</div><div class="s-lbl">Allowances</div></td>
        <td class="t-purple"><div class="s-val">K {{ number_format($summary['total_tax'], 2) }}</div><div class="s-lbl">PAYE Tax</div></td>
        <td class="t-red"><div class="s-val">K {{ number_format($summary['total_deductions'], 2) }}</div><div class="s-lbl">Total Deductions</div></td>
        <td class="t-dark"><div class="s-val">K {{ number_format($summary['total_net'], 2) }}</div><div class="s-lbl">Net Payroll</div></td>
    </tr>
</table>

{{-- Department breakdown --}}
@if($byDepartment->count() > 1)
<div class="sec">Department Summary</div>
<table class="dept">
    <thead>
        <tr>
            <th>Department</th>
            <th class="r">Employees</th>
            <th class="r">Total Basic (K)</th>
            <th class="r">Net Payroll (K)</th>
            <th class="r">% of Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($byDepartment as $dName => $d)
        <tr>
            <td>{{ $dName }}</td>
            <td class="r">{{ $d['count'] }}</td>
            <td class="r">{{ number_format($d['total_basic'], 2) }}</td>
            <td class="r">{{ number_format($d['total_net'],   2) }}</td>
            <td class="r">{{ $summary['total_net'] > 0 ? number_format(($d['total_net']/$summary['total_net'])*100,1) : '0.0' }}%</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="b">TOTAL</td>
            <td class="r b">{{ $summary['total_employees'] }}</td>
            <td class="r b">{{ number_format($summary['total_basic'], 2) }}</td>
            <td class="r b">{{ number_format($summary['total_net'],   2) }}</td>
            <td class="r b">100.0%</td>
        </tr>
    </tfoot>
</table>
@endif

{{-- Employee detail table (11 columns, no # counter) --}}
<div class="sec">Employee Payroll Detail &mdash; {{ $payrolls->count() }} record(s)</div>
<table class="pay">
    <colgroup>
        <col style="width:7%">
        <col style="width:17%">
        <col style="width:12%">
        <col style="width:10%">
        <col style="width:9%">
        <col style="width:9%">
        <col style="width:8%">
        <col style="width:9%">
        <col style="width:9%">
        <col style="width:6%">
        <col style="width:7%">
    </colgroup>
    <thead>
        <tr>
            <th>Emp. ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Designation</th>
            <th class="r">Basic (K)</th>
            <th class="r">Allow. (K)</th>
            <th class="r">PAYE (K)</th>
            <th class="r">Deduct. (K)</th>
            <th class="r">Net Pay (K)</th>
            <th class="c">Status</th>
            <th>Pay Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payrolls as $p)
        @php $bc2 = ['pending'=>'b-pending','processed'=>'b-processed','paid'=>'b-paid']; @endphp
        <tr class="{{ $p->status }}">
            <td><code>{{ optional($p->employee)->employee_id ?? '—' }}</code></td>
            <td class="b">{{ optional(optional($p->employee)->user)->name ?? '—' }}</td>
            <td>{{ optional(optional($p->employee)->department)->name ?? '—' }}</td>
            <td>{{ optional($p->employee)->designation ?? '—' }}</td>
            <td class="r">{{ number_format($p->basic_salary,    2) }}</td>
            <td class="r tg">{{ number_format($p->allowances ?? 0, 2) }}</td>
            <td class="r tc">{{ number_format($p->tax ?? 0,        2) }}</td>
            <td class="r tc">{{ number_format($p->deductions ?? 0, 2) }}</td>
            <td class="r b">{{ number_format($p->net_pay,         2) }}</td>
            <td class="c"><span class="badge {{ $bc2[$p->status] ?? 'b-all' }}">{{ ucfirst($p->status) }}</span></td>
            <td>{{ $p->payment_date ? $p->payment_date->format('d M Y') : '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:10px;color:#888">No payroll records for this period.</td>
        </tr>
        @endforelse
    </tbody>
    @if($payrolls->count())
    <tfoot>
        <tr>
            <td colspan="4" class="b">TOTALS</td>
            <td class="r b">{{ number_format($summary['total_basic'],       2) }}</td>
            <td class="r b tg">{{ number_format($summary['total_allowances'], 2) }}</td>
            <td class="r b tc">{{ number_format($summary['total_tax'],        2) }}</td>
            <td class="r b tc">{{ number_format($summary['total_deductions'], 2) }}</td>
            <td class="r b" style="color:#0B1F3A">{{ number_format($summary['total_net'], 2) }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- Signatures --}}
<table class="sigs">
    <tr>
        <td style="width:33%"><div class="sig-line">Prepared by</div></td>
        <td style="width:33%;text-align:center"><div class="sig-line" style="margin:20px auto 0">HR Manager</div></td>
        <td style="width:33%;text-align:right"><div class="sig-line" style="margin:20px 0 0 auto">Finance Director</div></td>
    </tr>
</table>

{{-- Footer --}}
<table class="foot">
    <tr>
        <td>Confidential &mdash; Human Resources Department &mdash; {{ $uniName }}</td>
        <td class="r">{{ date('F Y', mktime(0,0,0,$month,1,$year)) }} Payroll Report</td>
    </tr>
</table>

</body>
</html>
