<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Graduation Certificate</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #fff;
            color: #1a1a2e;
            width: 297mm;
            height: 210mm;
            position: relative;
        }

        /* Outer decorative border */
        .outer-border {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 3px solid #8B6914;
        }
        .inner-border {
            position: absolute;
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 1px solid #8B6914;
        }

        /* Content area */
        .content {
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
        }

        .top-section { width: 100%; }

        .university-name {
            font-size: 18pt;
            font-weight: bold;
            color: #0B1F3A;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 3pt;
        }
        .university-tagline {
            font-size: 8pt;
            color: #666;
            letter-spacing: 0.05em;
            margin-bottom: 8pt;
        }
        .divider {
            height: 2px;
            background: #8B6914;
            margin: 0 auto 8pt;
            width: 60mm;
        }
        .certificate-title {
            font-size: 26pt;
            color: #8B6914;
            font-weight: bold;
            letter-spacing: 0.05em;
            margin-bottom: 2pt;
        }
        .certificate-subtitle {
            font-size: 9pt;
            color: #555;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 6pt;
        }

        .middle-section { width: 100%; }

        .presented-to { font-size: 9pt; color: #666; margin-bottom: 6pt; }
        .student-name {
            font-size: 28pt;
            color: #0B1F3A;
            font-weight: bold;
            margin-bottom: 4pt;
        }
        .student-id { font-size: 8.5pt; color: #888; margin-bottom: 10pt; }
        .body-text { font-size: 9.5pt; color: #333; line-height: 1.6; margin-bottom: 6pt; }
        .program-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0B1F3A;
            margin: 4pt 0;
        }
        .program-level { font-size: 9pt; color: #666; }
        .honors {
            font-size: 10pt;
            color: #8B6914;
            font-style: italic;
            margin-top: 4pt;
        }

        .bottom-section {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-block { text-align: center; width: 55mm; }
        .signature-line { border-top: 1px solid #333; margin-bottom: 3pt; width: 45mm; margin-left: auto; margin-right: auto; }
        .signature-name { font-size: 8pt; font-weight: bold; }
        .signature-title { font-size: 7.5pt; color: #666; }

        .seal-block { text-align: center; }
        .seal-circle {
            width: 25mm;
            height: 25mm;
            border-radius: 50%;
            border: 2px solid #8B6914;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .seal-text { font-size: 7pt; color: #8B6914; text-transform: uppercase; letter-spacing: 0.05em; }

        .cert-details {
            font-size: 7.5pt;
            color: #888;
            margin-top: 6pt;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 70pt;
            color: rgba(11, 31, 58, 0.04);
            font-weight: bold;
            letter-spacing: 0.05em;
            white-space: nowrap;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL</div>
    <div class="outer-border"></div>
    <div class="inner-border"></div>

    <div class="content">
        <div class="top-section">
            <div class="university-name">{{ config('app.name', 'University Management System') }}</div>
            <div class="university-tagline">Knowledge &bull; Excellence &bull; Service</div>
            <div class="divider"></div>
            <div class="certificate-title">Certificate of Graduation</div>
            <div class="certificate-subtitle">This is to certify that</div>
        </div>

        <div class="middle-section">
            <div class="presented-to">Be it known to all that</div>
            <div class="student-name">{{ $application->student?->full_name }}</div>
            <div class="student-id">Student ID: {{ $application->student?->student_id }}</div>
            <div class="body-text">
                Having successfully fulfilled all the academic requirements and earned the required credits, is hereby awarded the degree of
            </div>
            <div class="program-name">{{ $application->program?->name }}</div>
            <div class="program-level">
                {{ $application->program?->level_label }}
                &bull; {{ $application->program?->department?->name }}
                @if($application->program?->department?->faculty?->name)
                &bull; {{ $application->program->department->faculty->name }}
                @endif
            </div>
            @php
                $cgpa = (float) $application->cgpa;
                $honors = match(true) {
                    $cgpa >= 3.7 => 'with First Class Honours',
                    $cgpa >= 3.3 => 'with Second Class Honours (Upper Division)',
                    $cgpa >= 2.7 => 'with Second Class Honours (Lower Division)',
                    default      => null,
                };
            @endphp
            @if($honors)
            <div class="honors">{{ $honors }}</div>
            @endif
            <div class="cert-details">
                CGPA: {{ number_format($application->cgpa, 2) }} &bull;
                Credits: {{ $application->credits_earned }} &bull;
                Academic Year: {{ $application->academicYear?->name }} &bull;
                Graduation Date: {{ $application->graduation_date?->format('d F Y') ?? now()->format('d F Y') }}
            </div>
        </div>

        <div class="bottom-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">Registrar</div>
                <div class="signature-title">Office of Academic Affairs</div>
            </div>

            <div class="seal-block">
                <div class="seal-circle">
                    <div class="seal-text">UNIVERSITY<br>SEAL</div>
                </div>
            </div>

            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">Vice-Chancellor</div>
                <div class="signature-title">{{ config('app.name', 'University') }}</div>
            </div>
        </div>
    </div>
</body>
</html>
