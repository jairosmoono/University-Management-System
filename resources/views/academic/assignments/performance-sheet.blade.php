<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Sheet — {{ $assignment->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11pt; color: #111; background: #fff; padding: 20px; }

        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 15pt; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        .header h2 { font-size: 12pt; font-weight: 600; margin-top: 4px; }
        .header p  { font-size: 9.5pt; color: #555; margin-top: 3px; }

        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0; border: 1px solid #ccc; margin-bottom: 18px; }
        .meta-grid .meta-row { display: contents; }
        .meta-grid .label { background: #f3f3f3; padding: 5px 10px; font-weight: 600; font-size: 9.5pt; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; }
        .meta-grid .value  { padding: 5px 10px; font-size: 9.5pt; border-bottom: 1px solid #ddd; }

        table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
        thead th { background: #222; color: #fff; padding: 7px 10px; text-align: left; font-weight: 600; font-size: 9pt; }
        tbody tr:nth-child(even) { background: #f8f8f8; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: 2px solid #333; }

        .badge-submitted  { color: #1558b0; font-weight: 600; }
        .badge-graded     { color: #1a7a3c; font-weight: 600; }
        .badge-late       { color: #c0392b; font-weight: 600; }
        .badge-pending    { color: #888; }

        .marks-cell { text-align: center; }
        .marks-num  { font-weight: 700; font-size: 11pt; }
        .marks-pct  { font-size: 8.5pt; color: #555; }

        .summary { display: flex; gap: 24px; margin-bottom: 16px; padding: 10px 14px; background: #f3f3f3; border: 1px solid #ddd; border-radius: 4px; }
        .summary .stat { text-align: center; }
        .summary .stat-val { font-size: 16pt; font-weight: 700; }
        .summary .stat-lbl { font-size: 8.5pt; color: #666; }

        .footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; display: flex; justify-content: space-between; font-size: 8.5pt; color: #888; }
        .signature-row { margin-top: 40px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 200px; border-top: 1px solid #333; padding-top: 5px; font-size: 9pt; }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            @page { margin: 18mm 15mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right;margin-bottom:12px">
    <button onclick="window.print()" style="padding:7px 18px;background:#222;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:11pt">
        🖨 Print / Save PDF
    </button>
    <button onclick="window.close()" style="padding:7px 14px;background:#eee;border:1px solid #ccc;border-radius:4px;cursor:pointer;font-size:11pt;margin-left:6px">
        Close
    </button>
</div>

{{-- Header --}}
<div class="header">
    <h1>{{ setting('university_name', config('app.name')) }}</h1>
    <h2>Assignment Performance Sheet</h2>
    <p>{{ setting('university_address', '') }}{{ setting('university_city') ? ', ' . setting('university_city') : '' }}</p>
</div>

{{-- Meta information --}}
<div class="meta-grid">
    <span class="label">Assignment Title</span>
    <span class="value">{{ $assignment->title }}</span>

    <span class="label">Course</span>
    <span class="value">
        {{ optional(optional($assignment->courseOffering)->course)->code }} —
        {{ optional(optional($assignment->courseOffering)->course)->name }}
    </span>

    <span class="label">Program</span>
    <span class="value">{{ optional(optional(optional($assignment->courseOffering)->course)->department)->name ?? '—' }}</span>

    <span class="label">Semester/Term</span>
    <span class="value">
        {{ optional(optional($assignment->courseOffering)->semester)->name }}
        @php $ay = optional(optional($assignment->courseOffering)->semester)->academicYear; @endphp
        {{ $ay ? '(' . $ay->name . ')' : '' }}
    </span>

    <span class="label">Lecturer</span>
    <span class="value">{{ optional(optional(optional($assignment->courseOffering)->lecturer)->user)->name ?? '—' }}</span>

    <span class="label">Total Marks</span>
    <span class="value">{{ $assignment->total_marks }}</span>

    <span class="label">Date Given (Created)</span>
    <span class="value">{{ $assignment->created_at->format('d F Y') }}</span>

    <span class="label">Due Date</span>
    <span class="value">{{ $assignment->due_date?->format('d F Y, H:i') }}</span>
</div>

{{-- Summary stats --}}
@php
    $submitted   = $submissionsMap->count();
    $graded      = $submissionsMap->where('status', 'graded')->count();
    $late        = $submissionsMap->where('status', 'late')->count();
    $notSubmitted = $enrolledStudents->count() - $submitted;
    $gradedSubs  = $submissionsMap->where('status', 'graded')->where('marks_obtained', '!=', null);
    $avg         = $gradedSubs->count() > 0 ? round($gradedSubs->avg('marks_obtained'), 1) : null;
    $highest     = $gradedSubs->count() > 0 ? $gradedSubs->max('marks_obtained') : null;
    $lowest      = $gradedSubs->count() > 0 ? $gradedSubs->min('marks_obtained') : null;
@endphp

<div class="summary">
    <div class="stat"><div class="stat-val">{{ $enrolledStudents->count() }}</div><div class="stat-lbl">Enrolled</div></div>
    <div class="stat"><div class="stat-val" style="color:#1558b0">{{ $submitted }}</div><div class="stat-lbl">Submitted</div></div>
    <div class="stat"><div class="stat-val" style="color:#1a7a3c">{{ $graded }}</div><div class="stat-lbl">Graded</div></div>
    <div class="stat"><div class="stat-val" style="color:#c0392b">{{ $late }}</div><div class="stat-lbl">Late</div></div>
    <div class="stat"><div class="stat-val" style="color:#c0392b">{{ $notSubmitted }}</div><div class="stat-lbl">Not Submitted</div></div>
    @if($avg !== null)
    <div class="stat"><div class="stat-val">{{ $avg }}</div><div class="stat-lbl">Avg Marks</div></div>
    <div class="stat"><div class="stat-val" style="color:#1a7a3c">{{ $highest }}</div><div class="stat-lbl">Highest</div></div>
    <div class="stat"><div class="stat-val" style="color:#c0392b">{{ $lowest }}</div><div class="stat-lbl">Lowest</div></div>
    @endif
</div>

{{-- Student list --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Date Submitted</th>
            <th style="text-align:center">Status</th>
            <th style="text-align:center">Marks Obtained</th>
            <th style="text-align:center">Percentage</th>
            <th>Feedback</th>
        </tr>
    </thead>
    <tbody>
        @foreach($enrolledStudents as $i => $student)
        @php $sub = $submissionsMap[$student->id] ?? null; @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $student->student_id }}</strong></td>
            <td>{{ optional($student->user)->name }}</td>
            <td>{{ $sub?->submitted_at?->format('d M Y H:i') ?? '—' }}</td>
            <td style="text-align:center">
                @if(!$sub)
                    <span class="badge-pending">Not Submitted</span>
                @elseif($sub->status === 'graded')
                    <span class="badge-graded">Graded</span>
                @elseif($sub->status === 'late')
                    <span class="badge-late">Late</span>
                @else
                    <span class="badge-submitted">Submitted</span>
                @endif
            </td>
            <td class="marks-cell">
                @if($sub && $sub->marks_obtained !== null)
                    <span class="marks-num">{{ $sub->marks_obtained }}</span>
                    <span style="color:#999"> / {{ $assignment->total_marks }}</span>
                @else
                    <span style="color:#ccc">—</span>
                @endif
            </td>
            <td class="marks-cell">
                @if($sub && $sub->marks_obtained !== null)
                    @php $pct = round(($sub->marks_obtained / $assignment->total_marks) * 100, 1); @endphp
                    <span class="marks-pct" style="color:{{ $pct >= 50 ? '#1a7a3c' : '#c0392b' }}">{{ $pct }}%</span>
                @else
                    <span style="color:#ccc">—</span>
                @endif
            </td>
            <td style="font-size:8.5pt;color:#555">{{ Str::limit($sub?->feedback ?? '', 50) ?: '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Signatures --}}
<div class="signature-row">
    <div class="signature-box">Lecturer's Signature</div>
    <div class="signature-box">HOD's Signature</div>
    <div class="signature-box">Date</div>
</div>

<div class="footer">
    <span>{{ setting('university_name', config('app.name')) }}</span>
    <span>Generated: {{ now()->format('d F Y, H:i') }}</span>
</div>

</body>
</html>
