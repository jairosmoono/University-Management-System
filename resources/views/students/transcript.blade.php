<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: Arial, sans-serif; font-size: 9pt; color: #1a1a1a; padding: 15mm 15mm 10mm; }
.header { text-align: center; border-bottom: 2pt solid #1a3a5c; padding-bottom: 5mm; margin-bottom: 6mm; }
.uni-name { font-size: 16pt; font-weight: 900; color: #1a3a5c; letter-spacing: 1pt; }
.uni-sub  { font-size: 8pt; color: #555; margin-top: 1mm; }
.doc-title { font-size: 12pt; font-weight: 700; color: #1a3a5c; margin-top: 3mm; text-transform: uppercase; letter-spacing: 2pt; }

.info-grid { display: table; width: 100%; margin-bottom: 6mm; }
.info-row  { display: table-row; }
.info-label, .info-value { display: table-cell; padding: 1.5mm 2mm; font-size: 8.5pt; }
.info-label { width: 35mm; color: #555; font-weight: 600; }
.info-value { font-weight: 400; }

.section-title {
    background: #1a3a5c;
    color: #fff;
    font-size: 8pt;
    font-weight: 700;
    padding: 2mm 3mm;
    margin-bottom: 0;
    text-transform: uppercase;
    letter-spacing: 0.5pt;
}
table { width: 100%; border-collapse: collapse; margin-bottom: 6mm; font-size: 8pt; }
thead th { background: #eef2f7; border: 0.5pt solid #ccc; padding: 2mm 2.5mm; text-align: left; font-weight: 700; font-size: 7.5pt; }
tbody td { border: 0.5pt solid #ddd; padding: 2mm 2.5mm; vertical-align: middle; }
tbody tr:nth-child(even) { background: #fafafa; }
.grade-cell { text-align: center; font-weight: 700; }
.gpa-row td { font-weight: 700; background: #eef2f7; }
.cumulative-row td { font-weight: 700; background: #dceeff; color: #1a3a5c; }

.summary-box {
    border: 1pt solid #1a3a5c;
    border-radius: 2mm;
    padding: 4mm;
    margin-bottom: 6mm;
    display: flex;
    justify-content: space-around;
}
.sum-item { text-align: center; }
.sum-val { font-size: 14pt; font-weight: 900; color: #1a3a5c; }
.sum-lbl { font-size: 7pt; color: #666; margin-top: 1mm; }

.footer { margin-top: 10mm; border-top: 1pt solid #ccc; padding-top: 4mm; display: flex; justify-content: space-between; font-size: 7.5pt; color: #666; }
.sig-line { border-top: 0.5pt solid #333; width: 50mm; margin-top: 12mm; text-align: center; padding-top: 1mm; font-size: 7pt; }
.watermark { position: fixed; bottom: 40mm; left: 50%; transform: translateX(-50%) rotate(-30deg); font-size: 48pt; color: rgba(26,58,92,0.07); font-weight: 900; white-space: nowrap; z-index: -1; }

@php
    function gradePoint(string $grade): float {
        return match(strtoupper(trim($grade))) {
            'A+','A'  => 4.0,
            'A-'      => 3.7,
            'B+'      => 3.3,
            'B'       => 3.0,
            'B-'      => 2.7,
            'C+'      => 2.3,
            'C'       => 2.0,
            'C-'      => 1.7,
            'D+'      => 1.3,
            'D'       => 1.0,
            default   => 0.0,
        };
    }
@endphp
</style>
</head>
<body>

<div class="watermark">OFFICIAL TRANSCRIPT</div>

<div class="header">
    <div class="uni-name">{{ strtoupper(setting('university_name', config('app.name'))) }}</div>
    <div class="uni-sub">{{ setting('university_address', '') }}{{ setting('university_city') ? ', ' . setting('university_city') : '' }} &bull; {{ setting('university_email', '') }}</div>
    <div class="doc-title">Official Academic Transcript</div>
</div>

<div class="info-grid">
    <div class="info-row">
        <div class="info-label">Student Name</div>
        <div class="info-value">{{ $student->full_name }}</div>
        <div class="info-label" style="padding-left:8mm">Student ID</div>
        <div class="info-value">{{ $student->student_id }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Program</div>
        <div class="info-value">{{ optional($student->program)->name }}</div>
        <div class="info-label" style="padding-left:8mm">Faculty</div>
        <div class="info-value">{{ optional($student->program?->department?->faculty)->name }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Enrollment Date</div>
        <div class="info-value">{{ $student->enrollment_date?->format('d M Y') ?? '—' }}</div>
        <div class="info-label" style="padding-left:8mm">Expected Graduation</div>
        <div class="info-value">{{ $student->expected_graduation?->format('d M Y') ?? '—' }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Status</div>
        <div class="info-value">{{ ucfirst($student->status) }}</div>
        <div class="info-label" style="padding-left:8mm">Printed On</div>
        <div class="info-value">{{ now()->format('d M Y') }}</div>
    </div>
</div>

@php
    $resultsBySemester = $student->finalResults
        ->groupBy(fn($r) => optional($r->courseOffering?->semester)->id);

    $totalCreditsEarned = 0;
    $totalQualityPoints  = 0;
@endphp

@forelse($resultsBySemester as $semesterId => $results)
@php
    $semester = $results->first()->courseOffering?->semester;
    $semCredits = 0; $semQP = 0;
    foreach ($results as $r) {
        $credits = $r->courseOffering?->course?->credits ?? 0;
        $gp = gradePoint($r->grade ?? '');
        $semCredits += $credits;
        $semQP += $credits * $gp;
    }
    $semGPA = $semCredits > 0 ? round($semQP / $semCredits, 2) : 0;
    $totalCreditsEarned += $semCredits;
    $totalQualityPoints += $semQP;
@endphp
<div class="section-title">
    {{ optional($semester)->name ?? 'Unknown Semester/Term' }}
    &mdash; {{ optional($semester?->academicYear)->name ?? '' }}
</div>
<table>
    <thead>
        <tr>
            <th style="width:15mm">Code</th>
            <th>Course Name</th>
            <th style="width:14mm;text-align:center">Credits</th>
            <th style="width:12mm;text-align:center">Grade</th>
            <th style="width:14mm;text-align:center">Points</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $result)
        @php
            $course  = $result->courseOffering?->course;
            $credits = $course?->credits ?? 0;
            $gp      = gradePoint($result->grade ?? '');
        @endphp
        <tr>
            <td>{{ $course?->code ?? '—' }}</td>
            <td>{{ $course?->name ?? '—' }}</td>
            <td style="text-align:center">{{ $credits }}</td>
            <td class="grade-cell">{{ $result->grade ?? '—' }}</td>
            <td style="text-align:center">{{ $credits > 0 ? number_format($credits * $gp, 1) : '—' }}</td>
        </tr>
        @endforeach
        <tr class="gpa-row">
            <td colspan="2" style="text-align:right">Semester/Term Totals</td>
            <td style="text-align:center">{{ $semCredits }}</td>
            <td style="text-align:center">{{ number_format($semGPA, 2) }}</td>
            <td style="text-align:center">{{ number_format($semQP, 1) }}</td>
        </tr>
    </tbody>
</table>
@empty
<p style="color:#888;font-style:italic;margin-bottom:6mm">No academic results recorded.</p>
@endforelse

@php
    $cgpa = $totalCreditsEarned > 0 ? round($totalQualityPoints / $totalCreditsEarned, 2) : 0;
@endphp

<div class="summary-box">
    <div class="sum-item">
        <div class="sum-val">{{ $totalCreditsEarned }}</div>
        <div class="sum-lbl">Credits Earned</div>
    </div>
    <div class="sum-item">
        <div class="sum-val">{{ number_format($cgpa, 2) }}</div>
        <div class="sum-lbl">Cumulative GPA</div>
    </div>
    <div class="sum-item">
        <div class="sum-val">{{ $student->year_of_study ?? '—' }}</div>
        <div class="sum-lbl">Current Year</div>
    </div>
    <div class="sum-item">
        <div class="sum-val">
            @if($cgpa >= 3.6) Distinction
            @elseif($cgpa >= 3.0) Merit
            @elseif($cgpa >= 2.0) Pass
            @else Credit Risk
            @endif
        </div>
        <div class="sum-lbl">Standing</div>
    </div>
</div>

<div class="footer">
    <div>
        <div class="sig-line">Registrar's Signature</div>
    </div>
    <div style="text-align:right;font-size:7pt;color:#999">
        <div>This is an official document of {{ setting('university_name', config('app.name')) }}.</div>
        <div>Alterations render this transcript invalid.</div>
        <div style="margin-top:2mm">Ref: {{ $student->student_id }}-{{ now()->format('Ymd') }}</div>
    </div>
</div>

</body>
</html>
