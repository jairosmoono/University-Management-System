<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Report</title>
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:8.5px; color:#1a1a1a; padding:14px 18px; }

    /* Header */
    table.hdr { width:100%; border-collapse:collapse; border-bottom:3px solid #0B1F3A; margin-bottom:10px; }
    table.hdr td { vertical-align:middle; padding-bottom:8px; }
    .org-logo { width:38px; height:38px; object-fit:contain; vertical-align:middle; margin-right:6px; }
    .org-init { display:inline-block; width:38px; height:38px; background:#0B1F3A; border-radius:50%; text-align:center; line-height:38px; color:#fff; font-size:14px; font-weight:bold; vertical-align:middle; margin-right:6px; }
    .org-name { font-size:12px; font-weight:bold; color:#0B1F3A; vertical-align:middle; }
    .org-sub  { font-size:7.5px; color:#666; }
    .doc-title { font-size:14px; font-weight:bold; color:#0B1F3A; text-transform:uppercase; letter-spacing:.6px; }
    .doc-meta  { font-size:7.5px; color:#555; margin-top:3px; }

    /* Filters */
    .filters { margin-bottom:8px; font-size:8px; color:#555; }
    .filters strong { color:#0B1F3A; }

    /* Summary */
    table.sum { width:100%; border-collapse:collapse; margin-bottom:10px; }
    table.sum td { border:1px solid #ddd; padding:5px 8px; text-align:center; border-top-width:3px; }
    .t-blue  { border-top-color:#0d6efd; }
    .t-green { border-top-color:#198754; }
    .t-warn  { border-top-color:#ffc107; }
    .t-info  { border-top-color:#0dcaf0; }
    .t-red   { border-top-color:#dc3545; }
    .s-val { font-size:11px; font-weight:bold; color:#0B1F3A; line-height:1; }
    .s-lbl { font-size:6.5px; color:#666; margin-top:1px; text-transform:uppercase; }

    /* Main table */
    table.data { width:100%; border-collapse:collapse; }
    table.data th { background:#0B1F3A; color:#fff; padding:4px 5px; font-size:7.5px; text-align:left; }
    table.data td { padding:3.5px 5px; border-bottom:1px solid #eee; font-size:8px; vertical-align:middle; overflow:hidden; word-wrap:break-word; }
    table.data tr:nth-child(even) td { background:#f9f9f9; }

    .badge { display:inline-block; padding:1px 5px; border-radius:2px; font-size:6.5px; font-weight:bold; }
    .b-active    { background:#d1e7dd; color:#0a3622; }
    .b-inactive  { background:#e2e3e5; color:#41464b; }
    .b-suspended { background:#f8d7da; color:#58151c; }
    .b-graduated { background:#cff4fc; color:#055160; }
    .b-dropped_out { background:#fff3cd; color:#856404; }
    .b-deferred  { background:#e0cffc; color:#3d0a6e; }

    code { font-family: DejaVu Sans Mono, monospace; font-size:7.5px; }

    /* Footer */
    table.foot { width:100%; border-collapse:collapse; margin-top:10px; border-top:1px solid #ddd; }
    table.foot td { font-size:6.5px; color:#999; padding-top:3px; }
    .r { text-align:right; }
</style>
</head>
<body>


<table class="hdr">
    <tr>
        <td style="width:60%">
            <?php if($logoSrc): ?>
                <img src="<?php echo e($logoSrc); ?>" class="org-logo" alt="">
            <?php else: ?>
                <span class="org-init"><?php echo e(strtoupper(substr($uniName,0,1))); ?></span>
            <?php endif; ?>
            <span class="org-name"><?php echo e($uniName); ?></span><br>
            <span class="org-sub" style="padding-left:44px">Student Records Report</span>
        </td>
        <td style="text-align:right">
            <div class="doc-title">Student Report</div>
            <div class="doc-meta">Generated: <?php echo e(now()->format('d M Y, H:i')); ?></div>
            <div class="doc-meta">Total Records: <strong><?php echo e($students->count()); ?></strong></div>
        </td>
    </tr>
</table>


<?php if(!empty($filters)): ?>
<div class="filters">
    <strong>Filters:</strong>
    <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo e($label); ?>: <strong><?php echo e($value); ?></strong><?php if(!$loop->last): ?> &nbsp;&bull;&nbsp; <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>


<?php
    $totalCount    = $students->count();
    $maleCount     = $students->where('gender', 'male')->count();
    $femaleCount   = $students->where('gender', 'female')->count();
    $activeCount   = $students->where('status', 'active')->count();
    $dropoutCount  = \App\Models\Student::where('status', 'dropped_out')->count();
?>
<table class="sum">
    <tr>
        <td class="t-blue">
            <div class="s-val"><?php echo e($totalCount); ?></div>
            <div class="s-lbl">Total Students</div>
        </td>
        <td class="t-green">
            <div class="s-val"><?php echo e($activeCount); ?></div>
            <div class="s-lbl">Active</div>
        </td>
        <td class="t-red">
            <div class="s-val"><?php echo e($dropoutCount); ?></div>
            <div class="s-lbl">Dropped Out</div>
        </td>
        <td class="t-info">
            <div class="s-val"><?php echo e($maleCount); ?></div>
            <div class="s-lbl">Male</div>
        </td>
        <td class="t-warn">
            <div class="s-val"><?php echo e($femaleCount); ?></div>
            <div class="s-lbl">Female</div>
        </td>
    </tr>
</table>


<table class="data">
    <thead>
        <tr>
            <th style="width:3%">#</th>
            <th style="width:10%">Student ID</th>
            <th style="width:16%">Full Name</th>
            <th style="width:14%">Email</th>
            <th style="width:8%">Phone</th>
            <th style="width:5%">Gender</th>
            <th style="width:10%">NRC Number</th>
            <th style="width:14%">Program</th>
            <th style="width:4%">Year</th>
            <th style="width:8%">Sponsor</th>
            <th style="width:8%">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($i + 1); ?></td>
            <td><code><?php echo e($s->student_id); ?></code></td>
            <td style="font-weight:600"><?php echo e(optional($s->user)->name ?? '—'); ?></td>
            <td style="font-size:7px"><?php echo e(optional($s->user)->email ?? '—'); ?></td>
            <td><?php echo e($s->phone ?: '—'); ?></td>
            <td><?php echo e(ucfirst($s->gender ?? '—')); ?></td>
            <td><?php echo e($s->national_id ?: '—'); ?></td>
            <td><?php echo e(optional($s->program)->name ?? '—'); ?></td>
            <td style="text-align:center"><?php echo e($s->year_of_study); ?></td>
            <td><?php echo e($s->sponsor ?: '—'); ?></td>
            <td>
                <?php
                    $bc = match($s->status) {
                        'active'    => 'b-active',
                        'inactive'  => 'b-inactive',
                        'suspended' => 'b-suspended',
                        'graduated' => 'b-graduated',
                        'dropped_out' => 'b-dropped_out',
                        'deferred'  => 'b-deferred',
                        default     => 'b-inactive',
                    };
                ?>
                <span class="badge <?php echo e($bc); ?>"><?php echo e($s->status === 'dropped_out' ? 'Dropped Out' : ucfirst($s->status)); ?></span>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="11" style="text-align:center;padding:14px;color:#888">No students found matching the criteria.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>


<table class="foot">
    <tr>
        <td>Confidential &mdash; <?php echo e($uniName); ?></td>
        <td class="r">Student Report &mdash; <?php echo e(now()->format('d M Y')); ?> &mdash; Page 1</td>
    </tr>
</table>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/students/report-pdf.blade.php ENDPATH**/ ?>