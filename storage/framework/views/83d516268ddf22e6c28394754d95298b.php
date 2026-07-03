<?php $__env->startSection('title', 'Students by Program Report'); ?>
<?php $__env->startSection('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Students by Program</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('reports.index')); ?>">Reports</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol></nav>
    </div>
    <a href="<?php echo e(route('reports.export', 'students')); ?>?<?php echo e(http_build_query(request()->query())); ?>"
        class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a>
</div>


<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end" id="filterForm">
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Faculty</label>
                <select name="faculty_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Faculties</option>
                    <?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($f->id); ?>" <?php echo e(request('faculty_id') == $f->id ? 'selected' : ''); ?>><?php echo e($f->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Program</label>
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->id); ?>" <?php echo e(request('program_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Gender</label>
                <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="male"   <?php echo e(request('gender') === 'male'   ? 'selected' : ''); ?>>Male</option>
                    <option value="female" <?php echo e(request('gender') === 'female' ? 'selected' : ''); ?>>Female</option>
                    <option value="other"  <?php echo e(request('gender') === 'other'  ? 'selected' : ''); ?>>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Student Type</label>
                <select name="admission_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="full-time" <?php echo e(request('admission_type') === 'full-time' ? 'selected' : ''); ?>>Full-Time</option>
                    <option value="part-time" <?php echo e(request('admission_type') === 'part-time' ? 'selected' : ''); ?>>Part-Time</option>
                    <option value="distance"  <?php echo e(request('admission_type') === 'distance'  ? 'selected' : ''); ?>>Distance</option>
                    <option value="online"    <?php echo e(request('admission_type') === 'online'    ? 'selected' : ''); ?>>Online</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Year</label>
                <select name="year_of_study" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    <?php for($y = 1; $y <= 6; $y++): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e(request('year_of_study') == $y ? 'selected' : ''); ?>>Year <?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Enrolled</label>
                <select name="enrollment_year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Any Year</option>
                    <?php for($yr = date('Y'); $yr >= date('Y') - 8; $yr--): ?>
                        <option value="<?php echo e($yr); ?>" <?php echo e(request('enrollment_year') == $yr ? 'selected' : ''); ?>><?php echo e($yr); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="active" <?php echo e($status === 'active' ? 'selected' : ''); ?>>Active</option>
                    <option value="all"    <?php echo e($status === 'all'    ? 'selected' : ''); ?>>All</option>
                    <option value="inactive"   <?php echo e($status === 'inactive'   ? 'selected' : ''); ?>>Inactive</option>
                    <option value="graduated"  <?php echo e($status === 'graduated'  ? 'selected' : ''); ?>>Graduated</option>
                    <option value="suspended"  <?php echo e($status === 'suspended'  ? 'selected' : ''); ?>>Suspended</option>
                    <option value="dropped_out" <?php echo e($status === 'dropped_out' ? 'selected' : ''); ?>>Dropped Out</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="<?php echo e(route('reports.students')); ?>" class="btn btn-sm btn-outline-secondary" title="Clear filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>


<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-primary"><?php echo e(number_format($stats['totalFiltered'])); ?></div>
                <div class="small text-muted text-uppercase">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-danger"><?php echo e(number_format($stats['totalDropouts'])); ?></div>
                <div class="small text-muted text-uppercase">Dropped Out</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Gender</div>
                <?php $__currentLoopData = $stats['byGender']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g => $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small"><?php echo e(ucfirst($g ?: 'Unknown')); ?></span>
                    <span class="badge bg-<?php echo e($g === 'male' ? 'primary' : ($g === 'female' ? 'danger' : 'secondary')); ?>"><?php echo e($cnt); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($stats['byGender']->isEmpty()): ?><span class="text-muted small">—</span><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Year of Study</div>
                <?php $__currentLoopData = $stats['byYear']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr => $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">Year <?php echo e($yr); ?></span>
                    <span class="badge bg-info text-dark"><?php echo e($cnt); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($stats['byYear']->isEmpty()): ?><span class="text-muted small">—</span><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Student Type</div>
                <?php $__currentLoopData = $stats['byType']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t => $cnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small"><?php echo e(ucfirst(str_replace('-', ' ', $t ?: 'Unknown'))); ?></span>
                    <span class="badge bg-success"><?php echo e($cnt); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($stats['byType']->isEmpty()): ?><span class="text-muted small">—</span><?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php if($stats['programBreakdown']->isNotEmpty() && !request('program_id')): ?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0"><i class="bi bi-bar-chart-line me-1 text-primary"></i> Enrollment by Program</h6>
        <span class="badge bg-secondary"><?php echo e($stats['programBreakdown']->count()); ?> programs</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Program</th>
                        <th>Department</th>
                        <th>Level</th>
                        <th class="text-center">Students</th>
                        <th style="min-width:140px">Share</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $stats['programBreakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $pct = $stats['totalFiltered'] > 0 ? round($row['count'] / $stats['totalFiltered'] * 100, 1) : 0;
                        $prog = $row['program'];
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo e(route('reports.students')); ?>?<?php echo e(http_build_query(array_merge(request()->query(), ['program_id' => $prog?->id]))); ?>"
                                class="text-decoration-none fw-semibold">
                                <?php echo e($prog?->name ?? '—'); ?>

                            </a>
                            <?php if($prog?->code): ?>
                                <small class="text-muted ms-1">(<?php echo e($prog->code); ?>)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?php echo e(optional($prog?->department)->name ?? '—'); ?></td>
                        <td>
                            <?php if($prog?->level): ?>
                                <span class="badge bg-light text-dark border"><?php echo e(ucfirst($prog->level)); ?></span>
                            <?php else: ?> —
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-bold"><?php echo e($row['count']); ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px">
                                    <div class="progress-bar bg-primary" style="width:<?php echo e($pct); ?>%"></div>
                                </div>
                                <span class="small text-muted" style="min-width:36px"><?php echo e($pct); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0">
            <i class="bi bi-people me-1 text-primary"></i>
            Student List
            <span class="badge bg-secondary ms-1"><?php echo e($students->total()); ?></span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Program</th>
                        <th>Department</th>
                        <th class="text-center">Year</th>
                        <th>Type</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="text-muted small"><?php echo e($students->firstItem() + $i); ?></td>
                        <td><code class="small"><?php echo e($student->student_id); ?></code></td>
                        <td><?php echo e($student->full_name); ?></td>
                        <td>
                            <?php if($student->gender): ?>
                                <span class="badge bg-<?php echo e($student->gender === 'male' ? 'primary' : ($student->gender === 'female' ? 'danger' : 'secondary')); ?> bg-opacity-75">
                                    <?php echo e(ucfirst($student->gender)); ?>

                                </span>
                            <?php else: ?> <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(optional($student->program)->name ?? '—'); ?></td>
                        <td class="text-muted small"><?php echo e(optional($student->program?->department)->name ?? '—'); ?></td>
                        <td class="text-center"><?php echo e($student->year_of_study ? 'Y'.$student->year_of_study : '—'); ?></td>
                        <td class="small"><?php echo e(ucfirst(str_replace('-', ' ', $student->admission_type ?? '—'))); ?></td>
                        <td class="small"><?php echo e($student->enrollment_date ? $student->enrollment_date->format('M Y') : '—'); ?></td>
                        <td>
                            <?php
                                $sc = match($student->status) {
                                    'active'    => 'success',
                                    'inactive'  => 'secondary',
                                    'graduated' => 'primary',
                                    'suspended' => 'warning',
                                    'dropped_out' => 'danger',
                                    default     => 'secondary',
                                };
                            ?>
                            <span class="badge bg-<?php echo e($sc); ?>"><?php echo e($student->status === 'dropped_out' ? 'Dropped Out' : ucfirst($student->status)); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="text-center text-muted py-5">
                        <i class="bi bi-search d-block fs-3 mb-2"></i> No students match the selected filters.
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3"><?php echo e($students->links()); ?></div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/reports/students.blade.php ENDPATH**/ ?>