<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admissions Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #222; background: #fff; }

        .header { background: #0B1F3A; color: white; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 16px; font-weight: bold; letter-spacing: 0.03em; }
        .header .meta { text-align: right; font-size: 9px; opacity: 0.8; }

        .summary { padding: 12px 20px 8px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; gap: 24px; }
        .summary-item { text-align: center; }
        .summary-item .val { font-size: 18px; font-weight: bold; }
        .summary-item .lbl { font-size: 8.5px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #c49200 !important; }
        .text-danger  { color: #dc3545 !important; }
        .text-primary { color: #0d6efd !important; }
        .text-info    { color: #17a2b8 !important; }

        .status-row { padding: 8px 20px; background: #fff; display: flex; gap: 16px; border-bottom: 1px solid #dee2e6; flex-wrap: wrap; }
        .status-pill { display: inline-flex; align-items: center; gap: 5px; font-size: 9px; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }

        .section-title { padding: 8px 20px 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #555; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0B1F3A; color: white; }
        thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; vertical-align: top; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-pending             { background: #fff3cd; color: #856404; }
        .badge-under_review        { background: #d1ecf1; color: #0c5460; }
        .badge-interview_scheduled { background: #e2d9f3; color: #432874; }
        .badge-approved            { background: #d4edda; color: #155724; }
        .badge-rejected            { background: #f8d7da; color: #721c24; }
        .badge-enrolled            { background: #cfe2ff; color: #084298; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 6px 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; font-size: 8px; color: #888; }
        .page-wrapper { padding: 0 0 30px; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>Admissions Report</h1>
        <div style="font-size:9px;opacity:0.75;margin-top:3px">Generated: {{ now()->format('d M Y H:i') }}</div>
    </div>
    <div class="meta">
        Total Records: {{ number_format($admissions->count()) }}<br>
        Report Date: {{ now()->format('d F Y') }}
    </div>
</div>

{{-- Summary bar --}}
<div class="summary">
    <div class="summary-item">
        <div class="val text-primary">{{ number_format($totalAll) }}</div>
        <div class="lbl">All Applications</div>
    </div>
    <div class="summary-item">
        <div class="val text-success">{{ number_format($byStatus['approved'] ?? 0) }}</div>
        <div class="lbl">Approved</div>
    </div>
    <div class="summary-item">
        <div class="val text-warning">{{ number_format($byStatus['pending'] ?? 0) }}</div>
        <div class="lbl">Pending</div>
    </div>
    <div class="summary-item">
        <div class="val text-danger">{{ number_format($byStatus['rejected'] ?? 0) }}</div>
        <div class="lbl">Rejected</div>
    </div>
    <div class="summary-item">
        <div class="val text-info">{{ number_format($byStatus['enrolled'] ?? 0) }}</div>
        <div class="lbl">Enrolled</div>
    </div>
    @if($acceptanceRate !== null)
    <div class="summary-item">
        <div class="val text-success">{{ $acceptanceRate }}%</div>
        <div class="lbl">Acceptance Rate</div>
    </div>
    @endif
</div>

{{-- Status legend --}}
<div class="status-row">
    @foreach(['pending'=>['#f0ad4e','Pending'],'under_review'=>['#17a2b8','Under Review'],'interview_scheduled'=>['#6f42c1','Interview Scheduled'],'approved'=>['#28a745','Approved'],'rejected'=>['#dc3545','Rejected'],'enrolled'=>['#0d6efd','Enrolled']] as $s => [$color, $label])
    @if(($byStatus[$s] ?? 0) > 0)
    <span class="status-pill">
        <span class="status-dot" style="background:{{ $color }}"></span>
        {{ $label }}: <strong>{{ $byStatus[$s] }}</strong>
    </span>
    @endif
    @endforeach
</div>

<div class="page-wrapper">
    <div class="section-title">Applications List</div>
    <table>
        <thead>
            <tr>
                <th>App. No.</th>
                <th>Applicant Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Semester/Term</th>
                <th>Gender</th>
                <th>Applied Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admissions as $admission)
            <tr>
                <td><strong>{{ $admission->application_number }}</strong></td>
                <td>{{ $admission->first_name }} {{ $admission->last_name }}</td>
                <td>{{ $admission->email }}</td>
                <td>{{ optional($admission->program)->name ?? '—' }}</td>
                <td>{{ optional($admission->semester)->name ?? '—' }}</td>
                <td style="text-transform:capitalize">{{ $admission->gender ?? '—' }}</td>
                <td>{{ $admission->created_at->format('d M Y') }}</td>
                <td>
                    <span class="badge badge-{{ $admission->status }}">
                        {{ ucwords(str_replace('_', ' ', $admission->status)) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#888;padding:16px">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="footer">
    <span>Confidential — For internal use only</span>
    <span>{{ config('app.name', 'University Management System') }} &bull; Admissions Report &bull; {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
