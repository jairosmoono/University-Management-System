<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }
    .page { padding: 24px 28px; }

    /* Header */
    .header { border-bottom: 3px solid #0B1F3A; padding-bottom: 12px; margin-bottom: 14px; }
    .header-inner { display: flex; justify-content: space-between; align-items: center; }
    .org-identity { display: flex; align-items: center; gap: 10px; }
    .org-logo { width: 52px; height: 52px; object-fit: contain; flex-shrink: 0; }
    .org-logo-placeholder { width: 52px; height: 52px; background: #0B1F3A; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .org-logo-placeholder span { color: #fff; font-size: 20px; font-weight: bold; }
    .org-name { font-size: 15px; font-weight: bold; color: #0B1F3A; }
    .org-sub  { font-size: 10px; color: #555; margin-top: 2px; }
    .slip-title { text-align: right; }
    .slip-title h2 { font-size: 14px; font-weight: bold; color: #8B0000; text-transform: uppercase; letter-spacing: 1px; }
    .slip-title p  { font-size: 10px; color: #555; margin-top: 3px; }

    /* Employee info */
    .info-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 14px; background: #f7f9fc; border: 1px solid #e0e6ef; border-radius: 3px; padding: 8px 10px; }
    .info-row  { display: table-row; }
    .info-cell { display: table-cell; width: 50%; padding: 3px 0; font-size: 10.5px; }
    .info-label { color: #555; width: 120px; display: inline-block; }
    .info-value { font-weight: bold; }

    /* Salary table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
    th { background: #0B1F3A; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
    td { padding: 4px 8px; border-bottom: 1px solid #eef0f3; font-size: 10.5px; }
    tr:last-child td { border-bottom: none; }
    .amount { text-align: right; font-family: monospace; }
    .section-header td { background: #e8edf3; font-weight: bold; color: #0B1F3A; font-size: 10px; text-transform: uppercase; padding: 5px 8px; letter-spacing: 0.4px; }
    .subtotal-row td { font-weight: bold; background: #f4f6f9; border-top: 1px solid #c5cdd8; }
    .total-row td { font-weight: bold; background: #dce4ef; border-top: 2px solid #0B1F3A; }
    .net-row td { background: #0B1F3A; color: #fff; font-weight: bold; font-size: 13px; padding: 7px 8px; }
    .indent td:first-child { padding-left: 18px; color: #333; }
    .deduct-amount { color: #8B0000; }

    /* Two-column layout */
    .two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 14px; }
    .col-half { display: table-cell; width: 50%; vertical-align: top; }

    /* Footer */
    .footer { border-top: 1px solid #ddd; margin-top: 14px; padding-top: 10px; display: table; width: 100%; }
    .sig-block { display: table-cell; width: 50%; font-size: 10px; }
    .sig-line  { border-top: 1px solid #333; margin-top: 28px; padding-top: 4px; width: 140px; }
    .badge-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
    .badge-paid      { background: #d4edda; color: #155724; }
    .badge-processed { background: #cce5ff; color: #004085; }
    .badge-pending   { background: #fff3cd; color: #856404; }
    .note { font-size: 9.5px; color: #666; margin-top: 10px; text-align: center; }
    .divider { border: none; border-top: 1px solid #ddd; margin: 14px 0; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="header-inner">
            <div class="org-identity">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" class="org-logo" alt="Logo">
                @else
                    <div class="org-logo-placeholder">
                        <span>{{ strtoupper(substr($uniName, 0, 1)) }}</span>
                    </div>
                @endif
                <div>
                    <div class="org-name">{{ $uniName }}</div>
                    <div class="org-sub">Human Resources Department</div>
                </div>
            </div>
            <div class="slip-title">
                <h2>Pay Slip</h2>
                <p>{{ date('F', mktime(0,0,0,$payroll->month,1)) }} {{ $payroll->year }}</p>
                @php $badgeClass = $payroll->status === 'paid' ? 'badge-paid' : ($payroll->status === 'processed' ? 'badge-processed' : 'badge-pending'); @endphp
                <span class="badge-status {{ $badgeClass }}">{{ ucfirst($payroll->status) }}</span>
            </div>
        </div>
    </div>

    {{-- Employee Details --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">Employee ID</span>
                <span class="info-value">{{ optional($payroll->employee)->employee_id ?? '—' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Department</span>
                <span class="info-value">{{ optional(optional($payroll->employee)->department)->name ?? '—' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">Name</span>
                <span class="info-value">{{ optional(optional($payroll->employee)->user)->name ?? '—' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Designation</span>
                <span class="info-value">{{ optional($payroll->employee)->designation ?? '—' }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-cell">
                <span class="info-label">Employment Type</span>
                <span class="info-value">{{ ucfirst(optional($payroll->employee)->employment_type ?? '—') }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Payment Date</span>
                <span class="info-value">{{ $payroll->payment_date ? $payroll->payment_date->format('d M Y') : '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Earnings & Deductions side by side --}}
    @php
        $gross           = (float)$payroll->basic_salary + (float)($payroll->allowances ?? 0);
        $totalAllowances = collect($allowanceRows)->sum('amount');
        $totalDeductions = collect($deductionRows)->sum('amount');
    @endphp

    <div class="two-col">
        {{-- LEFT: Earnings --}}
        <div class="col-half">
            <table>
                <thead><tr><th>Earnings</th><th class="amount">ZMW</th></tr></thead>
                <tbody>
                    <tr>
                        <td>Basic Salary</td>
                        <td class="amount">{{ number_format($payroll->basic_salary, 2) }}</td>
                    </tr>

                    @if(count($allowanceRows) > 0)
                        <tr class="section-header"><td colspan="2">Allowances</td></tr>
                        @foreach($allowanceRows as $row)
                        <tr class="indent">
                            <td>{{ $row['label'] }}</td>
                            <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="subtotal-row">
                            <td>Total Allowances</td>
                            <td class="amount">{{ number_format($totalAllowances, 2) }}</td>
                        </tr>
                    @else
                        <tr><td style="color:#888;font-style:italic">No allowances</td><td class="amount">0.00</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>Gross Earnings</td>
                        <td class="amount">{{ number_format($gross, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- RIGHT: Deductions --}}
        <div class="col-half">
            <table>
                <thead><tr><th>Deductions</th><th class="amount">ZMW</th></tr></thead>
                <tbody>
                    @forelse($deductionRows as $row)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="amount deduct-amount">{{ number_format($row['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td style="color:#888;font-style:italic" colspan="2">No deductions</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td>Total Deductions</td>
                        <td class="amount deduct-amount">{{ number_format($totalDeductions, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Net Pay banner --}}
    <table>
        <tfoot>
            <tr class="net-row">
                <td>NET PAY</td>
                <td class="amount">ZMW &nbsp;{{ number_format($payroll->net_pay, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="sig-block">
            <div class="sig-line">Employee Signature</div>
        </div>
        <div class="sig-block" style="text-align:right">
            <div class="sig-line" style="margin-left:auto;">Authorised Signatory</div>
        </div>
    </div>

    <p class="note">This is a computer-generated payslip. For queries contact the HR department.</p>
</div>
</body>
</html>
