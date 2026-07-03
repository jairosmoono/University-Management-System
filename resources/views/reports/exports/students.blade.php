<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students by Program Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #222; background: #fff; }

        /* Header */
        .header { background: #0B1F3A; color: white; padding: 14px 20px; display: flex; justify-content: space-between; align-items: flex-start; }
        .header h1 { font-size: 16px; font-weight: bold; letter-spacing: 0.03em; }
        .header .sub { font-size: 9px; opacity: 0.75; margin-top: 3px; }
        .header .meta { text-align: right; font-size: 9px; opacity: 0.8; line-height: 1.6; }

        /* Filter chips row */
        .filters { padding: 7px 20px; background: #eef2f7; border-bottom: 1px solid #d0d9e8; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .filter-chip { background: #fff; border: 1px solid #b8c5d6; border-radius: 12px; padding: 2px 9px; font-size: 8.5px; color: #333; }
        .filter-label { font-size: 8.5px; color: #666; font-weight: bold; margin-right: 2px; }

        /* Summary bar */
        .summary { padding: 10px 20px; background: #fff; border-bottom: 1px solid #dee2e6; display: flex; gap: 30px; }
        .summary-item .val { font-size: 18px; font-weight: bold; }
        .summary-item .lbl { font-size: 8.5px; color: #666; text-transform: uppercase; letter-spacing: 0.05em; }
        .text-primary { color: #0d6efd !important; }
        .text-success { color: #28a745 !important; }
        .text-danger  { color: #dc3545 !important; }
        .text-info    { color: #17a2b8 !important; }
        .text-warning { color: #c49200 !important; }
        .text-muted   { color: #888 !important; }

        /* Gender / year mini-stats */
        .meta-row { padding: 6px 20px 8px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; gap: 24px; flex-wrap: wrap; }
        .mini-stat { font-size: 9px; }
        .mini-stat .mk { font-weight: bold; margin-right: 3px; }

        /* Program breakdown table */
        .section-title { padding: 8px 20px 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #555; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0B1F3A; color: white; }
        thead th { padding: 6px 8px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
        thead th.center { text-align: center; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        tbody td { padding: 5px 8px; font-size: 9px; border-bottom: 1px solid #eee; vertical-align: middle; }
        tbody td.center { text-align: center; }

        /* Progress bar (program breakdown) */
        .bar-wrap { display: inline-block; width: 70px; height: 5px; background: #e9ecef; border-radius: 3px; vertical-align: middle; margin-right: 4px; }
        .bar-fill  { height: 5px; background: #0d6efd; border-radius: 3px; display: inline-block; }

        /* Badge */
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-active    { background: #d4edda; color: #155724; }
        .badge-inactive  { background: #e2e3e5; color: #383d41; }
        .badge-graduated { background: #cfe2ff; color: #084298; }
        .badge-suspended { background: #fff3cd; color: #856404; }
        .badge-dropped_out { background: #f8d7da; color: #721c24; }

        .badge-male   { background: #cfe2ff; color: #084298; }
        .badge-female { background: #f8d7da; color: #721c24; }
        .badge-other  { background: #e2e3e5; color: #383d41; }

        /* Program group header row */
        .prog-group { background: #e8eef6 !important; }
        .prog-group td { font-weight: bold; font-size: 9.5px; padding: 5px 8px; color: #0B1F3A; }

        /* Page break between sections */
        .page-break { page-break-before: always; }

        /* Footer */
        .footer { position: fixed; bottom: 0; left: 0; right: 0; padding: 6px 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; font-size: 8px; color: #888; }
        .page-wrapper { padding: 0 0 30px; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div>
        <h1>Students by Program Report</h1>
        <div class="sub">{{ config('app.name', 'University Management System') }}</div>
        <div class="sub" style="margin-top:4px">Generated: {{ now()->format('d M Y, H:i') }}</div>
    </div>
    <div class="meta">
        Total Records: {{ number_format($data->count()) }}<br>
        Status Filter: {{ ucfirst($status) }}<br>
        Report Date: {{ now()->format('d F Y') }}
    </div>
</div>

{{-- Active filters --}}
@if(!empty($filterLabels))
<div class="filters">
    <span class="filter-label">Filters:</span>
    @foreach($filterLabels as $lbl)
        <span class="filter-chip">{{ $lbl }}</span>
    @endforeach
</div>
@endif

{{-- Summary bar --}}
<div class="summary">
    <div class="summary-item">
        <div class="val text-primary">{{ number_format($data->count()) }}</div>
        <div class="lbl">Total Students</div>
    </div>
    <div class="summary-item">
        <div class="val text-success">{{ number_format($byGender->get('male', 0)) }}</div>
        <div class="lbl">Male</div>
    </div>
    <div class="summary-item">
        <div class="val text-danger">{{ number_format($byGender->get('female', 0)) }}</div>
        <div class="lbl">Female</div>
    </div>
    <div class="summary-item">
        <div class="val text-info">{{ $byProgram->count() }}</div>
        <div class="lbl">Programs</div>
    </div>
    <div class="summary-item">
        <div class="val text-danger">{{ number_format($totalDropouts) }}</div>
        <div class="lbl">Dropped Out</div>
    </div>
    @foreach($byType as $t => $n)
    <div class="summary-item">
        <div class="val text-muted">{{ number_format($n) }}</div>
        <div class="lbl">{{ ucfirst(str_replace('-', ' ', $t ?: 'Unknown')) }}</div>
    </div>
    @endforeach
</div>

{{-- By year mini-row --}}
@if($byYear->isNotEmpty())
<div class="meta-row">
    <span class="mini-stat" style="color:#555;font-weight:bold">Year of Study:</span>
    @foreach($byYear as $yr => $n)
    <span class="mini-stat"><span class="mk">Year {{ $yr }}:</span>{{ $n }}</span>
    @endforeach
</div>
@endif

{{-- ── Section 1: Program Breakdown Summary ─────────────────────────── --}}
<div class="page-wrapper">
    <div class="section-title">Enrollment by Program</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Program</th>
                <th>Code</th>
                <th>Level</th>
                <th>Department</th>
                <th class="center">Students</th>
                <th class="center">Male</th>
                <th class="center">Female</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byProgram as $i => $row)
            @php
                $prog = $row['program'];
                $pct  = $data->count() > 0 ? round($row['count'] / $data->count() * 100, 1) : 0;
                $barW = min(70, (int) ($pct * 0.7));
            @endphp
            <tr>
                <td class="text-muted">{{ $i + 1 }}</td>
                <td><strong>{{ $prog?->name ?? '—' }}</strong></td>
                <td class="text-muted">{{ $prog?->code ?? '—' }}</td>
                <td>{{ $prog?->level ? ucfirst($prog->level) : '—' }}</td>
                <td class="text-muted">{{ optional($prog?->department)->name ?? '—' }}</td>
                <td class="center"><strong>{{ $row['count'] }}</strong></td>
                <td class="center text-primary">{{ $row['male'] }}</td>
                <td class="center text-danger">{{ $row['female'] }}</td>
                <td>
                    <span class="bar-wrap"><span class="bar-fill" style="width:{{ $barW }}px"></span></span>
                    {{ $pct }}%
                </td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#888;padding:12px">No data.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Section 2: Detailed Student List grouped by program ──────── --}}
    <div class="section-title" style="margin-top:16px">Detailed Student List</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Full Name</th>
                <th class="center">Gender</th>
                <th class="center">Year</th>
                <th>Type</th>
                <th>Enrolled</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grouped = $data->groupBy('program_id');
                $rowNum  = 0;
            @endphp
            @foreach($grouped as $progId => $progStudents)
            @php
                $firstStudent = $progStudents->first();
                $progName     = optional($firstStudent->program)->name ?? 'No Program';
                $deptName     = optional($firstStudent->program?->department)->name ?? '';
            @endphp
            {{-- Program group header --}}
            <tr class="prog-group">
                <td colspan="8">
                    {{ $progName }}
                    @if($deptName) &nbsp;&mdash;&nbsp; <span style="font-weight:normal;font-size:9px;color:#555">{{ $deptName }}</span>@endif
                    <span style="float:right;font-weight:normal;font-size:9px;color:#555">{{ $progStudents->count() }} student{{ $progStudents->count() === 1 ? '' : 's' }}</span>
                </td>
            </tr>
            @foreach($progStudents as $student)
            @php $rowNum++; @endphp
            <tr>
                <td class="text-muted">{{ $rowNum }}</td>
                <td><strong>{{ $student->student_id ?? '—' }}</strong></td>
                <td>{{ $student->full_name }}</td>
                <td class="center">
                    @if($student->gender)
                        <span class="badge badge-{{ $student->gender }}">{{ ucfirst($student->gender) }}</span>
                    @else —
                    @endif
                </td>
                <td class="center">{{ $student->year_of_study ? 'Y'.$student->year_of_study : '—' }}</td>
                <td>{{ ucfirst(str_replace('-', ' ', $student->admission_type ?? '—')) }}</td>
                <td>{{ $student->enrollment_date ? $student->enrollment_date->format('M Y') : '—' }}</td>
                <td>
                    <span class="badge badge-{{ $student->status }}">{{ $student->status === 'dropped_out' ? 'Dropped Out' : ucfirst($student->status) }}</span>
                </td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <span>Confidential — For internal use only</span>
    <span>{{ config('app.name', 'University Management System') }} &bull; Students by Program Report &bull; {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
