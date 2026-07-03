<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; padding: 40px 50px; }

    /* Header */
    .header { border-bottom: 3px solid #003087; padding-bottom: 16px; margin-bottom: 24px; }
    .header-text h1 { font-size: 18px; color: #003087; font-weight: bold; letter-spacing: 0.5px; margin: 0; }
    .header-text p { font-size: 10px; color: #555; margin: 2px 0 0 0; }

    /* Reference box */
    .ref-block { margin-bottom: 20px; }
    .ref-block p { font-size: 10.5px; line-height: 1.8; }

    /* Watermark band */
    .status-band { background: #003087; color: #fff; text-align: center; padding: 6px 0; font-size: 13px; font-weight: bold; letter-spacing: 2px; margin-bottom: 22px; }

    /* Body */
    .salutation { font-size: 13px; font-weight: bold; margin-bottom: 12px; }
    .subject { font-size: 12px; font-weight: bold; text-decoration: underline; margin-bottom: 16px; }
    .body-text { line-height: 1.9; margin-bottom: 14px; font-size: 12px; }

    /* Offer details table */
    .details-table { width: 100%; border-collapse: collapse; margin: 18px 0; }
    .details-table td { padding: 7px 12px; font-size: 11.5px; border: 1px solid #ccc; }
    .details-table .label { background: #f0f4ff; font-weight: bold; width: 40%; color: #003087; }

    /* Conditions */
    .conditions-title { font-weight: bold; margin-bottom: 6px; font-size: 12px; }
    .conditions-list { padding-left: 18px; font-size: 11.5px; line-height: 1.9; }

    /* Signatures */
    .sig-section { margin-top: 40px; }
    .sig-table { width: 100%; }
    .sig-table td { width: 50%; vertical-align: bottom; padding: 0 10px; }
    .sig-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 6px; font-size: 10.5px; }
    .sig-name { font-weight: bold; font-size: 11px; }
    .sig-title { font-size: 10px; color: #555; }

    /* Footer */
    .footer { margin-top: 36px; border-top: 1px solid #ccc; padding-top: 10px; text-align: center; font-size: 9.5px; color: #777; }
</style>
</head>
<body>

@php
    $appNum   = $admission->application_number;
    $program  = optional($admission->program);
    $semester = optional($admission->semester);
    $today    = \Carbon\Carbon::now()->format('d F Y');
    $fullName = strtoupper(trim(($admission->first_name ?? '') . ' ' . ($admission->middle_name ? $admission->middle_name . ' ' : '') . ($admission->last_name ?? '')));

    // Load settings and university logo for PDF
    $settingsRaw  = \Illuminate\Support\Facades\Storage::exists('settings.json')
        ? json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true)
        : [];
    $uniName      = $settingsRaw['university_name'] ?? 'University Management System';
    $uniEmail     = $settingsRaw['university_email'] ?? 'admissions@university.ac.zm';
    $uniPhone     = $settingsRaw['university_phone'] ?? '+260 211 000 000';
    $uniAddress   = $settingsRaw['university_address'] ?? 'P.O. Box 12345, Lusaka, Zambia';

    $logoSrc = null;
    if (!empty($settingsRaw['logo_path'])) {
        $logoFile = storage_path('app/public/' . $settingsRaw['logo_path']);
        if (file_exists($logoFile)) {
            $mime    = mime_content_type($logoFile);
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
        }
    }
@endphp

<!-- Header -->
<div class="header">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td width="75" valign="middle">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" width="65" height="65" style="object-fit:contain;">
                @else
                    <svg width="65" height="65" viewBox="0 0 65 65" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="32.5" cy="32.5" r="32.5" fill="#003087"/>
                        <text x="32.5" y="38" text-anchor="middle" font-family="DejaVu Sans, sans-serif" font-size="26" font-weight="bold" fill="#ffffff">U</text>
                        <circle cx="32.5" cy="32.5" r="30" fill="none" stroke="#ffffff" stroke-width="1.5" stroke-dasharray="4,3"/>
                    </svg>
                @endif
            </td>
            <td valign="middle" style="padding-left:14px;">
                <div class="header-text">
                    <h1>{{ $uniName }}</h1>
                    <p>Office of the Registrar &bull; Admissions Division</p>
                    <p>{{ $uniAddress }} &bull; Tel: {{ $uniPhone }} &bull; {{ $uniEmail }}</p>
                </div>
            </td>
            <td valign="middle" align="right" style="font-size:10px; color:#555; white-space:nowrap;">
                <strong>Ref:</strong> {{ $appNum }}<br>
                <strong>Date:</strong> {{ $today }}
            </td>
        </tr>
    </table>
</div>

<!-- Status band -->
<div class="status-band">OFFER OF ADMISSION</div>

<!-- Addressee -->
<div class="ref-block">
    <p><strong>{{ $fullName }}</strong></p>
    @if($admission->address)<p>{{ $admission->address }}</p>@endif
    @if($admission->email)<p>Email: {{ $admission->email }}</p>@endif
    @if($admission->phone)<p>Phone: {{ $admission->phone }}</p>@endif
</div>

<!-- Salutation & Subject -->
<p class="salutation">Dear {{ ucfirst(strtolower($admission->first_name ?? 'Applicant')) }},</p>
<p class="subject">RE: OFFER OF ADMISSION &mdash; {{ strtoupper($program->name ?? 'PROGRAMME') }}</p>

<!-- Body -->
<p class="body-text">
    We are pleased to inform you that the University Admissions Committee has reviewed your application
    for admission and we are delighted to offer you a place in the programme detailed below, subject to
    the conditions stated herein.
</p>

<!-- Offer details -->
<table class="details-table">
    <tr>
        <td class="label">Application Number</td>
        <td>{{ $appNum }}</td>
    </tr>
    <tr>
        <td class="label">Programme</td>
        <td>{{ $program->name ?? '—' }} @if($program->code)({{ $program->code }})@endif</td>
    </tr>
    @if($program->level)
    <tr>
        <td class="label">Level of Study</td>
        <td>{{ ucfirst($program->level) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Semester/Term</td>
        <td>{{ $semester->name ?? '—' }}</td>
    </tr>
    @if($program->duration_years)
    <tr>
        <td class="label">Duration</td>
        <td>{{ $program->duration_years }} {{ $program->duration_years == 1 ? 'Year' : 'Years' }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">Mode of Study</td>
        <td>Full-Time</td>
    </tr>
</table>

<p class="body-text">
    To confirm your acceptance of this offer, please report to the Admissions Office with this letter and
    the original copies of all your academic certificates and identification documents within
    <strong>14 days</strong> of the date of this letter. Failure to do so may result in your offer being withdrawn.
</p>

<!-- Conditions -->
<p class="conditions-title">Conditions of This Offer:</p>
<ul class="conditions-list">
    <li>This offer is conditional upon verification of all submitted documents.</li>
    <li>Any misrepresentation of qualifications will result in immediate cancellation of admission.</li>
    <li>You must complete the registration process and pay the required tuition deposit by the deadline communicated by the Finance Office.</li>
    <li>You are required to comply with all University rules, regulations, and academic policies.</li>
</ul>

<p class="body-text" style="margin-top:14px;">
    We look forward to welcoming you to our University community. Should you require any further information,
    please do not hesitate to contact the Admissions Office.
</p>

<p class="body-text">Yours faithfully,</p>

<!-- Signatures -->
<div class="sig-section">
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line">
                    <div class="sig-name">{{ optional($admission->reviewer)->name ?? '___________________________' }}</div>
                    <div class="sig-title">Registrar / Head of Admissions</div>
                    <div class="sig-title">{{ $uniName }}</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-name">___________________________</div>
                    <div class="sig-title">Vice Chancellor</div>
                    <div class="sig-title">{{ $uniName }}</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Applicant acknowledgement -->
<div style="margin-top:30px; border: 1px dashed #999; padding: 12px;">
    <p style="font-size:10.5px; font-weight:bold; margin-bottom:8px;">APPLICANT ACKNOWLEDGEMENT (To be returned to the Admissions Office)</p>
    <p style="font-size:10.5px;">
        I, <strong>{{ $fullName }}</strong>, hereby accept/decline *(circle one) the offer of admission to
        <strong>{{ $program->name ?? '' }}</strong>.
    </p>
    <table style="width:100%; margin-top:18px;">
        <tr>
            <td style="width:50%; font-size:10px;">
                Signature: ____________________<br><br>
                Date: ____________________
            </td>
            <td style="width:50%; font-size:10px;">
                Student Number (if assigned): ____________________<br><br>
                &nbsp;
            </td>
        </tr>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    This is an official document issued by {{ $uniName }}. Ref: {{ $appNum }} &bull; Generated: {{ $today }}
</div>

</body>
</html>
