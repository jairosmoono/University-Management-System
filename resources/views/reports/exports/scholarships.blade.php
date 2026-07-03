<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scholarship Awards Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #222; background: #fff; }

        .header { background: #0B1F3A; color: white; padding: 14px 20px; display: flex; justify-content: space-between; align-items: flex-start; }
        .header h1 { font-size: 16px; font-weight: bold; letter-spacing: 0.03em; }
        .header .sub { font-size: 9px; opacity: 0.75; margin-top: 3px; }
        .header .meta { text-align: right; font-size: 9px; opacity: 0.8; line-height: 1.6; }

        .filters { padding: 7px 20px; background: #eef2f7; border-bottom: 1px solid #d0d9e8; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .filter-chip { background: #fff; border: 1px solid #b8c5d6; border-radius: 12px; padding: 2px 9px; font-size: 8.5px; color: #333; }
        .filter-label { font-size: 8.5px; color: #666; font-weight: bold; margin-right: 2px; }

        .summary { padding: 10px 20px; background: #fff; border-bottom: 1px solid #dee2e6; display: flex; gap: 30px; }
        .summary-item .val { font-size: 18px; font-weight: bold; }
        .summary-item .lbl { font-size: 8.5px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; }
        .text-primary { color: #0d6efd !important; }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #c49200 !important; }
        .text-info    { color: #17a2b8 !important; }
        .text-muted   { color: #888 !important; }

        .section-title { padding: 8px 20px 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #555; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0B1F3A; color: white; }
        thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tbody td.center { text-align: center; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-active    { background: #d4edda; color: #155724; }
        .badge-suspended { background: #fff3cd; color: #856404; }

        .bar-wrap { display: inline-block; width: 60px; height: 5px; background: #e9ecef; border-radius: 3px; vertical-align: middle; margin-right: 4px; }
        .bar-fill  { height: 5px; background: #0d6efd; border-radius: 3px; display: inline-block; }

        .sch-group { background: #e8eef6 !important; }
        .sch-group td { font-weight: bold; font-size: 9.5px; padding: 5px 8px; color: #0B1F3A; }

        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 6px 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; font-size: 8px; color: #888; }
        .page-wrapper { padding: 0 0 30px; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>Scholarship Awards Report</h1>
        <div class="sub">{{ config('app.name', 'University Management System') }}</div>
        <div class="sub" style="margin-top:4px">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
    <div class="meta">
        Total Award Records: {{ number_format($totalAwards) }}<br>
        Active Awards: {{ number_format($activeAwards) }}<br>
        Unique Students: {{ number_format($uniqueStudents) }}<br>
        Report Date: {{ now()->format('d F Y') }}
    </div>
</div>

@if(!empty($filterLabels))
<div class="filters">
    <span class="filter-label">Filters:</span>
    @foreach($filterLabels as $lbl)
        <span class="filter-chip">{{ $lbl }}</span>
    @endforeach
</div>
@endif

<div class="summary">
    <div class="summary-item">
        <div class="val text-primary">{{ number_format($totalAwards) }}</div>
        <div class="lbl">Total Awards</div>
    </div>
    <div class="summary-item">
        <div class="val text-success">{{ number_format($activeAwards) }}</div>
        <div class="lbl">Active</div>
    </div>
    <div class="summary-item">
        <div class="val text-warning">{{ number_format($totalAwards - $activeAwards) }}</div>
        <div class="lbl">Suspended</div>
    </div>
    <div class="summary-item">
        <div class="val text-info">{{ number_format($uniqueStudents) }}</div>
        <div class="lbl">Unique Students</div>
    </div>
    <div class="summary-item">
        <div class="val text-muted">{{ number_format($byScholarship->count()) }}</div>
        <div class="lbl">Scholarships</div>
    </div>
</div>

<div class="page-wrapper">

    {{-- Breakdown by scholarship --}}
    @if($byScholarship->isNotEmpty())
    <div class="section-title">Awards by Scholarship</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Scholarship Name</th>
                <th>Type</th>
                <th>Coverage</th>
                <th class="center">Total Awards</th>
                <th class="center">Active</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byScholarship as $i => $row)
            @php
                $sch  = $row['scholarship'];
                $pct  = $totalAwards > 0 ? round($row['count'] / $totalAwards * 100, 1) : 0;
                $barW = min(60, (int) ($pct * 0.6));
            @endphp
            <tr>
                <td class="text-muted">{{ $loop->iteration }}</td>
                <td><strong>{{ $sch?->name ?? '—' }}</strong></td>
                <td>{{ ucfirst($sch?->type ?? '—') }}</td>
                <td class="text-muted">
                    @if($sch?->coverage_type)
                        {{ ucfirst(str_replace('_', ' ', $sch->coverage_type)) }}
                        @if($sch->coverage_value) — {{ $sch->coverage_value }}{{ str_contains(strtolower($sch->coverage_type), 'percent') ? '%' : '' }}@endif
                    @else —
                    @endif
                </td>
                <td class="center"><strong>{{ $row['count'] }}</strong></td>
                <td class="center text-success">{{ $row['active'] }}</td>
                <td>
                    <span class="bar-wrap"><span class="bar-fill" style="width:{{ $barW }}px"></span></span>
                    {{ $pct }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Detailed awards grouped by scholarship --}}
    <div class="section-title" style="margin-top:16px">Detailed Award Records</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Program</th>
                <th>Department</th>
                <th>Award Date</th>
                <th>Awarded By</th>
                <th class="center">Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $data->groupBy('scholarship_id');
                $rowNum  = 0;
            @endphp
            @forelse($grouped as $schId => $schAwards)
            @php
                $schName = $schAwards->first()->scholarship?->name ?? 'Unknown Scholarship';
                $covType = $schAwards->first()->scholarship?->coverage_type;
                $covVal  = $schAwards->first()->scholarship?->coverage_value;
            @endphp
            <tr class="sch-group">
                <td colspan="9">
                    {{ $schName }}
                    @if($covType)
                        &nbsp;&mdash;&nbsp;
                        <span style="font-weight:normal;font-size:9px;color:#555">
                            {{ ucfirst(str_replace('_', ' ', $covType)) }}
                            @if($covVal) {{ $covVal }}{{ str_contains(strtolower($covType), 'percent') ? '%' : '' }}@endif
                        </span>
                    @endif
                    <span style="float:right;font-weight:normal;font-size:9px;color:#555">{{ $schAwards->count() }} award{{ $schAwards->count() === 1 ? '' : 's' }}</span>
                </td>
            </tr>
            @foreach($schAwards as $award)
            @php $rowNum++; @endphp
            <tr>
                <td class="text-muted">{{ $rowNum }}</td>
                <td><strong>{{ $award->student?->student_id ?? '—' }}</strong></td>
                <td>{{ $award->student?->user?->name ?? '—' }}</td>
                <td>{{ $award->student?->program?->name ?? '—' }}</td>
                <td class="text-muted">{{ $award->student?->program?->department?->name ?? '—' }}</td>
                <td>{{ $award->award_date?->format('d M Y') ?? '—' }}</td>
                <td class="text-muted">{{ $award->awardedBy?->name ?? '—' }}</td>
                <td class="center">
                    <span class="badge badge-{{ $award->status }}">{{ ucfirst($award->status) }}</span>
                </td>
                <td class="text-muted">{{ $award->notes ? \Illuminate\Support\Str::limit($award->notes, 50) : '—' }}</td>
            </tr>
            @endforeach
            @empty
            <tr><td colspan="9" style="text-align:center;color:#888;padding:12px">No award records found.</td></tr>
            @endforelse
        </tbody>
    </table>

</div>

<div class="footer">
    <span>Confidential — For internal use only</span>
    <span>{{ config('app.name', 'University Management System') }} &bull; Scholarship Awards Report &bull; {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
