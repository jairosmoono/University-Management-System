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

<?php $chunks = $students->chunk(8); ?>

<?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageStudents): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<table cellpadding="0" cellspacing="0"
       style="width:210mm; border-collapse:collapse; table-layout:fixed;">

    <?php $rows = $pageStudents->chunk(2); ?>
    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
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
        ?>

        <td style="width:105mm; padding:9mm 9.7mm; vertical-align:top;">
        <div style="width:85.6mm; height:54mm; border:0.3pt dashed #b0bec5; overflow:hidden;">
        <div style="width:85.6mm; height:54mm; background:#fff; overflow:hidden; border:0.5pt solid #ccc; position:relative;">

            
            <div style="position:absolute; top:1.5mm; right:2mm; width:8mm; height:8mm;
                        background:<?php echo e($residColor); ?>; border-radius:50%;
                        text-align:center; line-height:8mm;
                        color:#fff; font-size:14pt; font-weight:900;"><?php echo e($residLabel); ?></div>

            
            <div style="text-align:center; padding:2mm 3mm 1.5mm; border-bottom:0.8pt solid #333;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:12mm; vertical-align:middle;">
                        <?php if($logoSrc): ?>
                        <img src="<?php echo e($logoSrc); ?>" style="width:10mm; height:10mm; object-fit:contain;">
                        <?php else: ?>
                        <div style="width:10mm; height:10mm; background:#0B1F3A; border-radius:50%;
                                    text-align:center; line-height:10mm; color:#fff;
                                    font-size:6pt; font-weight:900;"><?php echo e(strtoupper(substr($uniName,0,2))); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="vertical-align:middle; text-align:center; padding-right:12mm;">
                        <div style="font-size:7.5pt; font-weight:900; color:#000;
                                    text-transform:uppercase; line-height:1.2;"><?php echo e($uniName); ?></div>
                        <?php if(!empty($uniAddr)): ?>
                        <div style="font-size:4.5pt; color:#444; margin-top:0.3mm;"><?php echo e($uniAddr); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($uniPhone) || !empty($uniEmail)): ?>
                        <div style="font-size:4pt; color:#555; margin-top:0.2mm;">
                            <?php if(!empty($uniPhone)): ?><?php echo e($uniPhone); ?><?php endif; ?>
                            <?php if(!empty($uniPhone) && !empty($uniEmail)): ?> | <?php endif; ?>
                            <?php if(!empty($uniEmail)): ?><?php echo e($uniEmail); ?><?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                </table>
            </div>

            
            <div style="text-align:center; padding:1mm 0 0.8mm;
                        font-size:7pt; font-weight:900; color:#000;
                        letter-spacing:0.5pt; text-decoration:underline;">STUDENT ID CARD</div>

            
            <div style="padding:0.5mm 3mm 2mm;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:20mm; vertical-align:top; padding-right:2.5mm;">
                        <div style="width:18mm; height:22mm; overflow:hidden;
                                    border:0.6pt solid #999; background:#f0f0f0;">
                            <?php if($photoSrc): ?>
                            <img src="<?php echo e($photoSrc); ?>" style="width:18mm; height:22mm; display:block; object-fit:cover;">
                            <?php else: ?>
                            <table cellpadding="0" cellspacing="0" style="width:18mm;height:22mm;border-collapse:collapse;">
                            <tr><td style="text-align:center;vertical-align:middle;">
                                <div style="font-size:5pt;color:#999;">No Photo</div>
                            </td></tr>
                            </table>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="vertical-align:top;">
                        <table cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:6pt; width:100%;">
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm; width:16mm;">Name:</td>
                                <td style="color:#000; padding-bottom:0.8mm;"><?php echo e($sName); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Student No:</td>
                                <td style="color:#000; padding-bottom:0.8mm;"><?php echo e($sId); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">NRC No:</td>
                                <td style="color:#000; padding-bottom:0.8mm;"><?php echo e($sNrc); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Programme:</td>
                                <td style="color:#000; font-size:5.5pt; padding-bottom:0.8mm;"><?php echo e($sProgram); ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight:700; color:#000; padding-bottom:0.8mm;">Gender:</td>
                                <td style="color:#000; padding-bottom:0.8mm;"><?php echo e($sGender); ?></td>
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

            
            <div style="border-top:0.5pt solid #ccc; padding:0.8mm 3mm;">
                <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse; font-size:5pt;">
                <tr>
                    <td style="vertical-align:middle; color:#333; line-height:1.5;">
                        <div><span style="font-weight:700;">Intake:</span> <?php echo e($intake); ?></div>
                        <div><span style="font-weight:700;">Type:</span> <?php echo e($studentType); ?></div>
                    </td>
                    <td style="text-align:right; vertical-align:middle;">
                        <div style="display:inline-block; background:#0B1F3A; color:#fff;
                                    padding:0.6mm 2mm; border-radius:0.8mm;
                                    font-size:5pt; font-weight:700;">Valid until: <?php echo e($validUntil); ?></div>
                    </td>
                </tr>
                </table>
            </div>

        </div>
        </div>
        </td>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php for($p = $row->count(); $p < 2; $p++): ?>
            <td style="width:105mm;"></td>
        <?php endfor; ?>

    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</table>

<?php if(!$loop->last): ?>
    <div style="page-break-after:always;"></div>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/students/cards-bulk.blade.php ENDPATH**/ ?>