<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seating Plan — {{ $examination->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; padding: 16px 20px; }

        /* Header */
        table.hdr { width: 100%; border-collapse: collapse; border-bottom: 3px solid #0B1F3A; margin-bottom: 12px; }
        table.hdr td { vertical-align: middle; padding-bottom: 10px; }
        .org-logo { width: 40px; height: 40px; object-fit: contain; vertical-align: middle; margin-right: 8px; }
        .org-init { display: inline-block; width: 40px; height: 40px; background: #0B1F3A; border-radius: 50%;
                    text-align: center; line-height: 40px; color: #fff; font-size: 16px; font-weight: bold;
                    vertical-align: middle; margin-right: 8px; }
        .org-name { font-size: 13px; font-weight: bold; color: #0B1F3A; vertical-align: middle; }
        .org-sub  { font-size: 8px; color: #666; }
        .doc-title { font-size: 15px; font-weight: bold; color: #0B1F3A; text-transform: uppercase; letter-spacing: 0.8px; }
        .doc-meta  { font-size: 8px; color: #555; margin-top: 4px; }

        /* Meta grid — styles kept minimal; cells use inline styles for DomPDF compatibility */

        /* Count badge */
        .count-badge { display: inline-block; background: #0B1F3A; color: #fff;
                       padding: 3px 10px; border-radius: 3px; font-size: 9.5px; margin-bottom: 10px; }

        /* Main table */
        table.seats { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.seats thead th { background: #0B1F3A; color: #fff; padding: 5px 6px;
                                font-size: 8.5px; text-align: left; text-transform: uppercase; letter-spacing: 0.4px; }
        table.seats tbody tr:nth-child(even) td { background: #f8f8f8; }
        table.seats tbody tr { border-bottom: 1px solid #e8e8e8; }
        table.seats tbody td { padding: 5px 6px; vertical-align: middle; font-size: 9.5px; }

        .seat-no { font-weight: bold; color: #0B1F3A; text-align: center; width: 40px; }
        .student-id { font-family: DejaVu Sans Mono, monospace; font-size: 8.5px; color: #444; }

        /* Signatures */
        table.sigs { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.sigs td { font-size: 8.5px; color: #444; text-align: center; vertical-align: bottom; padding: 0 10px; }
        .sig-line { border-top: 1px solid #555; margin-top: 28px; padding-top: 4px; }

        /* Footer */
        table.foot { width: 100%; border-collapse: collapse; margin-top: 12px; border-top: 1px solid #ddd; }
        table.foot td { font-size: 7.5px; color: #999; padding-top: 4px; }
        .r { text-align: right; }
    </style>
</head>
<body>

@php
    $course    = optional(optional($examination->courseOffering)->course);
    $courseTxt = ($course->code ? $course->code . ' — ' . $course->name : ($course->name ?? '—'));
    $startTime = $examination->start_time ? \Carbon\Carbon::parse($examination->start_time)->format('H:i') : '—';
    $endTime   = $examination->end_time   ? \Carbon\Carbon::parse($examination->end_time)->format('H:i')   : '—';
    $semName   = optional(optional($examination->courseOffering)->semester)->name ?? '—';
    $invigilator = optional(optional($examination->invigilator)->user)->name ?: '—';
@endphp

{{-- Header --}}
<table class="hdr">
    <tr>
        <td style="width:65%">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" class="org-logo" alt="">
            @else
                <span class="org-init">{{ strtoupper(substr($uniName, 0, 1)) }}</span>
            @endif
            <span class="org-name">{{ $uniName }}</span><br>
            <span class="org-sub" style="padding-left:48px">
                @if($uniAddr){{ $uniAddr }}@endif
                @if($uniAddr && ($uniPhone || $uniEmail)) &bull; @endif
                @if($uniPhone){{ $uniPhone }}@endif
                @if($uniPhone && $uniEmail) | @endif
                @if($uniEmail){{ $uniEmail }}@endif
            </span>
        </td>
        <td style="text-align:right;vertical-align:middle">
            <div class="doc-meta">SEATING PLAN</div>
            <div class="doc-title">{{ $examination->name }}</div>
            <div class="doc-meta">Generated: {{ now()->format('d M Y, H:i') }}</div>
        </td>
    </tr>
</table>

{{-- Meta details --}}
@php
    $lbl = 'padding:3px 6px;border:1px solid #ddd;font-size:7.5px;color:#777;text-transform:uppercase;letter-spacing:0.4px;background:#f5f6f8';
    $val = 'padding:5px 6px;border:1px solid #ddd;font-size:10px;font-weight:bold;color:#111';
    $byProgram = $students->groupBy(fn($s) => optional($s->program)->name ?? 'Unassigned');
@endphp
@php
    $pillLbl = 'background:#0B1F3A;color:#fff;padding:4px 8px;font-size:7.5px;text-transform:uppercase;letter-spacing:0.4px;font-weight:bold';
    $pillVal = 'background:#eef1f6;color:#111;padding:4px 10px;font-size:9.5px;font-weight:bold';
@endphp
<table style="border-collapse:collapse;margin-bottom:14px">
    <tr>
        <td style="{{ $pillLbl }}">EXAMINATION</td>
        <td style="{{ $pillVal }}">{{ $examName }}</td>
        <td style="width:12px"></td>
        <td style="{{ $pillLbl }}">DATE</td>
        <td style="{{ $pillVal }}">{{ $examDate }}</td>
        <td style="width:12px"></td>
        <td style="{{ $pillLbl }}">CANDIDATES</td>
        <td style="{{ $pillVal }}">{{ $students->count() }}</td>
    </tr>
</table>

{{-- Students grouped by programme --}}
@if($students->isEmpty())
<p style="text-align:center;padding:20px;color:#999;">No registered students found for this examination.</p>
@else
@php $seatNo = 1; @endphp
@foreach($byProgram as $programName => $group)

{{-- Programme section header --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:4px;margin-top:10px">
    <tr>
        <td style="background:#C9A84C;color:#0B1F3A;padding:5px 8px;font-size:9px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px">
            {{ $programName }}
        </td>
        <td style="background:#C9A84C;color:#0B1F3A;padding:5px 8px;font-size:9px;text-align:right;white-space:nowrap;font-weight:bold">
            {{ $group->count() }} candidate(s)
        </td>
    </tr>
</table>

<table class="seats">
    <thead>
        <tr>
            <th style="width:40px">Seat</th>
            <th style="width:110px">Student ID</th>
            <th>Full Name</th>
            <th style="width:90px">Signature</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group as $student)
        <tr>
            <td class="seat-no">{{ str_pad($seatNo++, 2, '0', STR_PAD_LEFT) }}</td>
            <td class="student-id">{{ $student->student_id }}</td>
            <td style="font-weight:600">{{ optional($student->user)->name ?? '—' }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif

{{-- Signatures --}}
<table class="sigs">
    <tr>
        <td style="width:33%">
            <div class="sig-line">Invigilator: {{ $invigilator }}</div>
        </td>
        <td style="width:33%">
            <div class="sig-line">Head of Department</div>
        </td>
        <td style="width:33%">
            <div class="sig-line">Registrar</div>
        </td>
    </tr>
</table>

{{-- Footer --}}
<table class="foot">
    <tr>
        <td>Confidential &mdash; For Official Use Only &mdash; {{ $uniName }}</td>
        <td class="r">Seating Plan &mdash; {{ $examination->name }} &mdash; {{ now()->format('d M Y') }}</td>
    </tr>
</table>

</body>
</html>
