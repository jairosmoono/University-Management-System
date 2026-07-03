<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Preview — {{ $application->student?->full_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #e8e8e8;
            font-family: sans-serif;
            min-height: 100vh;
        }

        /* ── Fixed top bar ───────────────────────────────────────────── */
        #preview-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 56px;
            background: #0B1F3A;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,.35);
        }
        #preview-bar .bar-left   { display: flex; align-items: center; gap: 14px; }
        #preview-bar .bar-right  { display: flex; align-items: center; gap: 10px; }
        #preview-bar .preview-badge {
            background: #f59e0b;
            color: #1a1a1a;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            padding: 3px 10px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        #preview-bar .bar-title  { font-size: 14px; font-weight: 600; }
        #preview-bar .bar-sub    { font-size: 12px; color: #94a3b8; }

        /* ── Scrollable area below bar ───────────────────────────────── */
        #preview-scroll {
            margin-top: 56px;
            padding: 32px 24px 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ── Certificate wrapper ─────────────────────────────────────── */
        #cert-wrapper {
            position: relative;
            /* A4 landscape: 297 × 210 mm = 1122 × 794 px at 96 dpi */
            width: 1122px;
            height: 794px;
            transform-origin: top center;
            box-shadow: 0 8px 40px rgba(0,0,0,.28);
        }

        /* ── "PREVIEW" diagonal stamp ────────────────────────────────── */
        #preview-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 110px;
            font-weight: 900;
            color: rgba(245, 158, 11, 0.13);
            letter-spacing: .1em;
            white-space: nowrap;
            pointer-events: none;
            z-index: 10;
            text-transform: uppercase;
            font-family: 'Georgia', serif;
        }

        /* ── Info strip below certificate ───────────────────────────── */
        #cert-info {
            width: 1122px;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
        }

        /* ── Actual certificate styles (matches certificate.blade.php) ─ */
        .cert-body {
            font-family: 'Georgia', serif;
            background: #fff;
            color: #1a1a2e;
            width: 1122px;
            height: 794px;
            position: relative;
            overflow: hidden;
        }
        .outer-border {
            position: absolute;
            top: 30px; left: 30px; right: 30px; bottom: 30px;
            border: 3px solid #8B6914;
        }
        .inner-border {
            position: absolute;
            top: 46px; left: 46px; right: 46px; bottom: 46px;
            border: 1px solid #8B6914;
        }
        .content {
            position: absolute;
            top: 57px; left: 57px; right: 57px; bottom: 57px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
        }
        .top-section    { width: 100%; }
        .middle-section { width: 100%; }
        .bottom-section { width: 100%; display: flex; justify-content: space-between; align-items: flex-end; }

        .university-name {
            font-size: 22px; font-weight: bold; color: #0B1F3A;
            letter-spacing: .08em; text-transform: uppercase; margin-bottom: 4px;
        }
        .university-tagline { font-size: 11px; color: #666; letter-spacing: .05em; margin-bottom: 10px; }
        .divider { height: 2px; background: #8B6914; margin: 0 auto 10px; width: 226px; }
        .certificate-title {
            font-size: 36px; color: #8B6914; font-weight: bold;
            letter-spacing: .05em; margin-bottom: 3px;
        }
        .certificate-subtitle {
            font-size: 12px; color: #555; letter-spacing: .12em;
            text-transform: uppercase; margin-bottom: 8px;
        }
        .presented-to   { font-size: 12px; color: #666; margin-bottom: 8px; }
        .student-name   { font-size: 38px; color: #0B1F3A; font-weight: bold; margin-bottom: 5px; }
        .student-id     { font-size: 11px; color: #888; margin-bottom: 14px; }
        .body-text      { font-size: 13px; color: #333; line-height: 1.6; margin-bottom: 8px; }
        .program-name   { font-size: 22px; font-weight: bold; color: #0B1F3A; margin: 5px 0; }
        .program-level  { font-size: 12px; color: #666; }
        .honors         { font-size: 14px; color: #8B6914; font-style: italic; margin-top: 5px; }
        .cert-details   { font-size: 10px; color: #888; margin-top: 8px; }
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 94px; color: rgba(11, 31, 58, 0.04);
            font-weight: bold; letter-spacing: .05em;
            white-space: nowrap; pointer-events: none;
        }
        .signature-block    { text-align: center; width: 207px; }
        .signature-line     { border-top: 1px solid #333; margin-bottom: 4px; width: 170px; margin-left: auto; margin-right: auto; }
        .signature-name     { font-size: 11px; font-weight: bold; }
        .signature-title    { font-size: 10px; color: #666; }
        .seal-block         { text-align: center; }
        .seal-circle        {
            width: 94px; height: 94px; border-radius: 50%;
            border: 2px solid #8B6914;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto;
        }
        .seal-text { font-size: 9px; color: #8B6914; text-transform: uppercase; letter-spacing: .05em; }

        @media print {
            #preview-bar, #cert-info  { display: none !important; }
            body                       { background: #fff; }
            #preview-scroll            { margin: 0; padding: 0; }
            #cert-wrapper              { box-shadow: none; transform: none !important; }
            #preview-stamp             { display: none !important; }
        }
    </style>
</head>
<body>

{{-- ── Top preview bar ──────────────────────────────────────────────── --}}
<div id="preview-bar">
    <div class="bar-left">
        <span class="preview-badge">Preview</span>
        <div>
            <div class="bar-title">{{ $application->student?->full_name }}</div>
            <div class="bar-sub">{{ $application->program?->name }} &bull; Application #{{ $application->id }}</div>
        </div>
    </div>
    <div class="bar-right">
        <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($application->status) }} me-2">
            {{ \App\Models\GraduationApplication::statusLabel($application->status) }}
        </span>
        @if(is_numeric($application->id) && in_array($application->status, ['approved', 'graduated']))
        <a href="{{ route('graduation.certificate', $application->id) }}"
           class="btn btn-sm btn-warning text-dark fw-semibold">
            <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
        </a>
        @endif
        @if($application->id === 'SAMPLE')
        <span class="badge bg-secondary me-1">Sample Preview</span>
        @endif
        <button onclick="window.close()" class="btn btn-sm btn-outline-light ms-1">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>

{{-- ── Scrollable preview area ──────────────────────────────────────── --}}
<div id="preview-scroll">

    {{-- Certificate card --}}
    <div id="cert-wrapper">
        {{-- Preview stamp overlay --}}
        <div id="preview-stamp">PREVIEW</div>

        {{-- Certificate body --}}
        <div class="cert-body">
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
                        Having successfully fulfilled all the academic requirements and earned the required credits,
                        is hereby awarded the degree of
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
                        $cgpa   = (float) $application->cgpa;
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
        </div>
        {{-- /cert-body --}}
    </div>
    {{-- /cert-wrapper --}}

    {{-- Info strip --}}
    <div id="cert-info">
        <div class="bg-white rounded-3 shadow-sm px-4 py-3 small" style="flex:1">
            <div class="fw-semibold text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.06em">Student Details</div>
            <div class="row g-2">
                <div class="col-6"><span class="text-muted">Name:</span> <strong>{{ $application->student?->full_name }}</strong></div>
                <div class="col-6"><span class="text-muted">ID:</span> <strong>{{ $application->student?->student_id }}</strong></div>
                <div class="col-6"><span class="text-muted">Program:</span> <strong>{{ $application->program?->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Department:</span> <strong>{{ $application->program?->department?->name }}</strong></div>
                <div class="col-6"><span class="text-muted">CGPA:</span> <strong>{{ number_format($application->cgpa, 2) }}</strong>
                    @if($honors ?? null) <span class="text-warning ms-1">({{ $honors }})</span> @endif
                </div>
                <div class="col-6"><span class="text-muted">Credits:</span> <strong>{{ $application->credits_earned }}</strong></div>
            </div>
        </div>
        <div class="bg-white rounded-3 shadow-sm px-4 py-3 small" style="flex:1">
            <div class="fw-semibold text-muted text-uppercase mb-2" style="font-size:11px;letter-spacing:.06em">Application Details</div>
            <div class="row g-2">
                <div class="col-6"><span class="text-muted">Application #:</span> <strong>{{ $application->id }}</strong></div>
                <div class="col-6"><span class="text-muted">Status:</span>
                    <span class="badge bg-{{ \App\Models\GraduationApplication::statusColor($application->status) }}">
                        {{ \App\Models\GraduationApplication::statusLabel($application->status) }}
                    </span>
                </div>
                <div class="col-6"><span class="text-muted">Academic Year:</span> <strong>{{ $application->academicYear?->name }}</strong></div>
                <div class="col-6"><span class="text-muted">Graduation Date:</span>
                    <strong>{{ $application->graduation_date?->format('d M Y') ?? '—' }}</strong>
                </div>
                @if($application->ceremony)
                <div class="col-6"><span class="text-muted">Ceremony:</span> <strong>{{ $application->ceremony->name }}</strong></div>
                @endif
                @if($application->approvedBy)
                <div class="col-6"><span class="text-muted">Approved by:</span> <strong>{{ $application->approvedBy->name }}</strong></div>
                @endif
            </div>
        </div>
    </div>

</div>
{{-- /preview-scroll --}}

<script>
(function () {
    function scaleCert() {
        const wrapper = document.getElementById('cert-wrapper');
        const info    = document.getElementById('cert-info');
        // 1122px native width + 48px padding on each side
        const available = window.innerWidth - 48;
        const scale     = Math.min(1, available / 1122);
        wrapper.style.transform = 'scale(' + scale + ')';
        // Adjust the apparent height so the page doesn't leave a gap
        wrapper.style.marginBottom = ((794 * scale) - 794) + 'px';
        if (info) info.style.width = Math.min(1122, available) + 'px';
    }
    scaleCert();
    window.addEventListener('resize', scaleCert);
})();
</script>
</body>
</html>
