<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-speedometer2 me-2" style="color:var(--secondary)"></i>Dashboard</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success fs-7 px-3 py-2">
            <i class="bi bi-circle-fill me-1" style="font-size:0.5rem"></i>
            <?php echo e($currentAcademicYear?->name ?? 'No Active Year'); ?>

        </span>
        <?php if($currentSemester): ?>
        <span class="badge bg-primary fs-7 px-3 py-2"><?php echo e($currentSemester->name); ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- === STAT CARDS === -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label text-muted mb-1">Total Students</div>
                    <div class="stat-value"><?php echo e(number_format($totalStudents)); ?></div>
                    <div class="stat-change text-success mt-1"><i class="bi bi-arrow-up-short"></i>Active enrollment</div>
                </div>
                <div class="stat-icon" style="background:rgba(11,31,58,0.1); color:#0B1F3A"><i class="bi bi-person-badge"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label text-muted mb-1">Total Staff</div>
                    <div class="stat-value"><?php echo e(number_format($totalStaff)); ?></div>
                    <div class="stat-change text-muted mt-1"><i class="bi bi-people"></i> Academic &amp; Admin</div>
                </div>
                <div class="stat-icon" style="background:rgba(139,0,0,0.1); color:#8B0000"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label text-muted mb-1">Faculties</div>
                    <div class="stat-value"><?php echo e($totalFaculties); ?></div>
                    <div class="stat-change text-muted mt-1"><i class="bi bi-building"></i> <?php echo e($totalDepartments); ?> Depts</div>
                </div>
                <div class="stat-icon" style="background:rgba(13,110,253,0.1); color:#0d6efd"><i class="bi bi-building"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label text-muted mb-1">Programs</div>
                    <div class="stat-value"><?php echo e($totalPrograms); ?></div>
                    <div class="stat-change text-muted mt-1"><i class="bi bi-book"></i> <?php echo e($totalCourses); ?> Courses</div>
                </div>
                <div class="stat-icon" style="background:rgba(25,135,84,0.1); color:#198754"><i class="bi bi-mortarboard"></i></div>
            </div>
        </div>
    </div>
</div>

<?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|finance-officer')): ?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3" style="border-left:4px solid #198754">
            <div class="stat-label text-muted mb-1">Revenue This Year</div>
            <div class="stat-value text-success"><?php echo e(formatCurrency($totalRevenue ?? 0)); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3" style="border-left:4px solid #dc3545">
            <div class="stat-label text-muted mb-1">Outstanding Balance</div>
            <div class="stat-value text-danger"><?php echo e(formatCurrency($outstandingBalance ?? 0)); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3" style="border-left:4px solid #ffc107">
            <div class="stat-label text-muted mb-1">Open Support Tickets</div>
            <div class="stat-value text-warning"><?php echo e($openTickets ?? 0); ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <!-- Enrollment Trend Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Enrollment Trend</h6>
                <span class="badge bg-light text-dark">Last 5 Years</span>
            </div>
            <div class="card-body">
                <canvas id="enrollmentChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-3">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|registrar')): ?>
                <a href="<?php echo e(route('students.create')); ?>" class="btn btn-outline-primary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus"></i>Add New Student
                </a>
                <a href="<?php echo e(route('admissions.create')); ?>" class="btn btn-outline-secondary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-plus"></i>New Application
                </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|finance-officer')): ?>
                <a href="<?php echo e(route('finance.payments.index')); ?>" class="btn btn-outline-success w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card"></i>Record Payment
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('announcements.create')); ?>" class="btn btn-outline-info w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-megaphone"></i>New Announcement
                </a>
                <a href="<?php echo e(route('support.create')); ?>" class="btn btn-outline-warning w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-headset"></i>Create Support Ticket
                </a>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-dark w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart"></i>View Reports
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Recent Students -->
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|registrar')): ?>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-person-badge me-2" style="color:var(--secondary)"></i>Recent Students</h6>
                <a href="<?php echo e(route('students.index')); ?>" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    <?php $__empty_1 = true; $__currentLoopData = $recentStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('students.show', $student)); ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3 px-3 py-2">
                        <img src="<?php echo e($student->photo_url); ?>" class="avatar avatar-sm rounded-circle" alt="">
                        <div class="flex-1 min-width-0">
                            <div class="fw-semibold" style="font-size:0.85rem"><?php echo e($student->full_name); ?></div>
                            <div class="text-muted" style="font-size:0.75rem"><?php echo e($student->student_id); ?> &bull; <?php echo e($student->program?->name); ?></div>
                        </div>
                        <span class="badge bg-<?php echo e($student->status === 'active' ? 'success' : 'secondary'); ?>" style="font-size:0.65rem"><?php echo e(ucfirst($student->status)); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted p-4">No students found</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Announcements -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-megaphone me-2 text-info"></i>Recent Announcements</h6>
                <a href="<?php echo e(route('announcements.index')); ?>" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('announcements.show', $ann)); ?>" class="list-group-item list-group-item-action px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-<?php echo e(['general'=>'secondary','academic'=>'primary','finance'=>'success','events'=>'info','emergency'=>'danger'][$ann->category] ?? 'secondary'); ?> mt-1" style="font-size:0.65rem"><?php echo e(ucfirst($ann->category)); ?></span>
                            <div class="flex-1">
                                <div class="fw-semibold" style="font-size:0.85rem"><?php echo e($ann->title); ?></div>
                                <div class="text-muted" style="font-size:0.75rem"><?php echo e($ann->created_at->diffForHumans()); ?></div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center text-muted p-4">No announcements</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|finance-officer')): ?>
    <!-- Recent Payments -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-credit-card me-2 text-success"></i>Recent Payments</h6>
                <a href="<?php echo e(route('finance.payments.index')); ?>" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentPayments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code><?php echo e($payment->reference_number); ?></code></td>
                            <td><?php echo e($payment->student?->full_name); ?></td>
                            <td class="fw-semibold text-success"><?php echo e(formatCurrency($payment->amount)); ?></td>
                            <td><span class="badge bg-light text-dark"><?php echo e(str_replace('_', ' ', $payment->payment_method)); ?></span></td>
                            <td><?php echo e($payment->payment_date->format('d M Y')); ?></td>
                            <td><?php echo statusBadge($payment->status); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No recent payments</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Enrollment Trend Chart
const enrollmentData = <?php echo json_encode($enrollmentTrend ?? [], 15, 512) ?>;
new Chart(document.getElementById('enrollmentChart'), {
    type: 'line',
    data: {
        labels: enrollmentData.map(d => d.year),
        datasets: [{
            label: 'Students Enrolled',
            data: enrollmentData.map(d => d.count),
            borderColor: '#0B1F3A',
            backgroundColor: 'rgba(11,31,58,0.08)',
            borderWidth: 2.5,
            pointBackgroundColor: '#8B0000',
            pointRadius: 5,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/dashboard/index.blade.php ENDPATH**/ ?>