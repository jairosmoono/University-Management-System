<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,Helvetica,sans-serif; background:#fff; }
</style>
</head>
<body>

@php $chunks = $students->chunk(8); @endphp

@foreach($chunks as $pageStudents)

<table cellpadding="0" cellspacing="0"
       style="width:210mm; border-collapse:collapse; table-layout:fixed;">

    @php $rows = $pageStudents->chunk(2); @endphp
    @foreach($rows as $row)
    <tr>
        @foreach($row as $student)
        @php
            $photoSrc = null;
            if ($student->photo) {
                $pf = storage_path('app/public/' . $student->photo);
                if (file_exists($pf)) {
                    $photoSrc = 'data:' . mime_content_type($pf) . ';base64,'
                              . base64_encode(file_get_contents($pf));
                }
            }
            $sName      = $student->full_name ?: '—';
            $sId        = $student->student_id ?: '—';
            $sNrc       = $student->national_id ?: '—';
            $sProgram   = Illuminate\Support\Str::limit(optional($student->program)->name ?: '—', 30, '');
            $sGender    = ucfirst($student->gender ?: '—');
            $hasHostel  = $student->hostelAllocation ? true : false;
            $residLabel = $hasHostel ? 'B' : 'D';
            $residColor = $hasHostel ? '#198754' : '#dc3545';

            $program     = $student->program;
            $durYears    = $program->duration_years ?? null;
            $durUnit     = $program->duration_unit ?? 'years';
            $enrollDate  = $student->enrollment_date;
            if ($enrollDate && $durYears) {
                $validUntil = $durUnit === 'months'
                    ? $enrollDate->copy()->addMonths((int)$durYears)->format('M Y')
                    : $enrollDate->copy()->addYears((int)$durYears)->format('M Y');
            } else {
                $validUntil = $student->expected_graduation?->format('M Y') ?? 'N/A';
            }
            $intake      = $enrollDate ? $enrollDate->format('M Y') : 'N/A';
            $studentType = ucfirst(str_replace('-', ' ', $student->admission_type ?? 'Full-Time'));
        @endphp

        <td style="width:105mm; padding:9mm 9.7mm; vertical-align:top;">
        <div style="width:85.6mm; height:54mm; border:0.3pt dashed #b0bec5; overflow:hidden;">
        <div style="width:85.6mm; height:54mm; background:#fff; overflow:hidden; border:0.5pt solid #ccc; position:relative;">

            {{-- Residence badge --}}
            <div style="position:absolute; top:1.5mm; right:2mm; width:8mm; height:8mm;
                        background:{{ $residColor }}; border-radius:50%;
                        text-align:center; line-height:8mm;
                        color:#fff; font-size:14pt; font-weight:900;">{{ $residLabel }}</div>

            {{-- Header --}}
            <div style="text-align:center; padding:2mm 3mm 1.5mm; border-bottom:0.8pt solid #333;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:12mm; vertical-align:middle;">
                        @if($logoSrc)
                        <img src="{{ $logoSrc }}" style="width:10mm; height:10mm; object-fit:contain;">
                        @else
                        <div style="width:10mm; height:10mm; background:#0B1F3A; border-radius:50%;
                                    text-align:center; line-height:10mm; color:#fff;
                                    font-size:6pt; font-weight:900;">{{ strtoupper(substr($uniName,0,2)) }}</div>
                        @endif
                    </td>
                    <td style="vertical-align:middle; text-align:center; padding-right:12mm;">
                        <div style="font-size:7.5pt; font-weight:900; color:#000;
                                    text-transform:uppercase; line-height:1.2;">{{ $uniName }}</div>
                        @if(!empty($uniAddr))
                        <div style="font-size:4.5pt; color:#444; margin-top:0.3mm;">{{ $uniAddr }}</div>
                        @endif
                        @if(!empty($uniPhone) || !empty($uniEmail))
                        <div style="font-size:4pt; color:#555; margin-top:0.2mm;">
                            @if(!empty($uniPhone)){{ $uniPhone }}@endif
                            @if(!empty($uniPhone) && !empty($uniEmail)) | @endif
                            @if(!empty($uniEmail)){{ $uniEmail }}@endif
                        </div>
                        @endif
                    </td>
                </tr>
                </table>
            </div>

            {{-- Title --}}
            <div style="text-align:center; padding:1mm 0 0.8mm;
                        font-size:7pt; font-weight:900; color:#000;
                        letter-spacing:0.5pt; text-decoration:underline;">STUDENT ID CARD</div>

            {{-- Body --}}
            <div style="padding:0.5mm 3mm 2mm;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:20mm; vertical-align:top; padding-right:2.5mm;">
                        <div style="width:18mm; height:22mm; overflow:hidden;
                                    border:0.6pt solid #999; background:#f0f0f0;">
                            @if($photoSrc)
                            <img src="{{ $photoSrc }}" style="width:18mm; height:22mm; display:block; object-fit:cover;">
                            @else
                            <table cellpadding="0" cellspacing="0" style="width:18mm;height:22mm;border-collapse:collapse;">
                            <tr><td style="text-align:center;vertical-align:middle;">
                                <div style="font-size:5pt;color:#999;">No Photo</div>
                            </td></tr>
                            </table>
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align:top;">
                        <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:6pt; width:100%;">
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm; width:16mm;">Name:</td>
                                <td style="color:#000; padding-bottom:0.8mm;">{{ $sName }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Student No:</td>
                                <td style="color:#000; padding-bottom:0.8mm;">{{ $sId }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">NRC No:</td>
                                <td style="color:#000; padding-bottom:0.8mm;">{{ $sNrc }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Programme:</td>
                                <td style="color:#000; font-size:5.5pt; padding-bottom:0.8mm;">{{ $sProgram }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Gender:</td>
                                <td style="color:#000; padding-bottom:0.8mm;">{{ $sGender }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top:1.5mm;">
                                    <div style="font-size:6pt; font-weight:700; color:#000;">Principal's signature:__________</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>
            </div>

            {{-- Footer --}}
            <div style="border-top:0.5pt solid #ccc; padding:0.8mm 3mm;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:5pt;">
                <tr>
                    <td style="vertical-align:middle; color:#333; line-height:1.5;">
                        <div><span style="font-weight:700;">Intake:</span> {{ $intake }}</div>
                        <div><span style="font-weight:700;">Type:</span> {{ $studentType }}</div>
                    </td>
                    <td style="text-align:right; vertical-align:middle;">
                        <div style="display:inline-block; background:#0B1F3A; color:#fff;
                                    padding:0.6mm 2mm; border-radius:0.8mm;
                                    font-size:5pt; font-weight:700;">Valid until: {{ $validUntil }}</div>
                    </td>
                </tr>
                </table>
            </div>

        </div>
        </div>
        </td>

        @endforeach

        @for($p = $row->count(); $p < 2; $p++)
            <td style="width:105mm;"></td>
        @endfor

    </tr>
    @endforeach

</table>

@if(!$loop->last)
    <div style="page-break-after:always;"></div>
@endif

@endforeach
</body>
</html>
