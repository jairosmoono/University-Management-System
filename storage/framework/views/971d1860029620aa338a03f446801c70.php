<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <h4 class="mb-1">Reports & Analytics</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol></nav>
</div>

<div class="row g-4">
    <!-- Academic Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:var(--primary);width:50px;height:50px">
                        <i class="bi bi-mortarboard fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Academic Reports</h6>
                        <small class="text-muted">Enrollment, results, GPA</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('reports.students')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Enrollment Report</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.academic')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Academic Results Summary</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.academic')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>GPA/CGPA Analysis</a></li>
                    <li><a href="<?php echo e(route('reports.attendance')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Attendance Report</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Finance Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#28a745;width:50px;height:50px">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Finance Reports</h6>
                        <small class="text-muted">Revenue, outstanding, payroll</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('finance.reports.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Revenue Summary</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('finance.reports.revenue')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Collection Report</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('finance.reports.outstanding')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Outstanding Bills</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.scholarships')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Scholarship Awards</a></li>
                    <li><a href="<?php echo e(route('hr.payroll.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Payroll Summary</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Student Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#17a2b8;width:50px;height:50px">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Student Reports</h6>
                        <small class="text-muted">Students, admissions, alumni</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('reports.students')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Students by Program</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.admissions')); ?>" class="text-decoration-none d-flex align-items-center gap-2 fw-semibold text-primary"><i class="bi bi-chevron-right text-primary small"></i>Admissions Report</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('alumni.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Alumni Employment</a></li>
                    <li><a href="<?php echo e(route('hostel.allocations.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Hostel Occupancy</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Admissions Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#e83e8c;width:50px;height:50px">
                        <i class="bi bi-person-plus fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Admissions Reports</h6>
                        <small class="text-muted">Applications, status, trends</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('reports.admissions')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>All Applications</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.admissions')); ?>?status=pending" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Pending Applications</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.admissions')); ?>?status=approved" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Approved Applicants</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.admissions')); ?>?status=rejected" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Rejected Applications</a></li>
                    <li><a href="<?php echo e(route('reports.export', 'admissions')); ?>" class="text-decoration-none d-flex align-items-center gap-2 text-danger"><i class="bi bi-file-earmark-pdf small"></i>Export Full Report (PDF)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- HR Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:var(--secondary);width:50px;height:50px">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">HR Reports</h6>
                        <small class="text-muted">Staff, leaves, payroll</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('hr.employees.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Staff Directory</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('hr.leave.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Leave Summary</a></li>
                    <li><a href="<?php echo e(route('hr.payroll.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Payroll Analysis</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Library Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#6f42c1;width:50px;height:50px">
                        <i class="bi bi-book fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Library Reports</h6>
                        <small class="text-muted">Books, borrowings, fines</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('library.books.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Book Inventory</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('library.borrowings.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Borrowing Statistics</a></li>
                    <li><a href="<?php echo e(route('library.borrowings.overdue')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Overdue Books</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Hostel Reports -->
    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|hostel-manager')): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#20c997;width:50px;height:50px">
                        <i class="bi bi-house-door fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Hostel Reports</h6>
                        <small class="text-muted">Occupancy, allocations, checkouts</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><a href="<?php echo e(route('reports.hostel')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Occupancy Summary</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.hostel')); ?>?status=active" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Active Allocations</a></li>
                    <li class="mb-2"><a href="<?php echo e(route('reports.hostel')); ?>?status=vacated" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Vacated History</a></li>
                    <li><a href="<?php echo e(route('hostel.allocations.occupancy')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Room Occupancy Map</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- System Reports -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white p-3" style="background:#fd7e14;width:50px;height:50px">
                        <i class="bi bi-gear fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">System Reports</h6>
                        <small class="text-muted">Audit logs, system activity</small>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('super-admin')): ?>
                    <li class="mb-2"><a href="<?php echo e(route('admin.audit-logs')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Audit Log</a></li>
                    <?php endif; ?>
                    <li class="mb-2"><a href="<?php echo e(route('reports.login-activity')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>Login Activity</a></li>
                    <li><a href="<?php echo e(route('reports.index')); ?>" class="text-decoration-none d-flex align-items-center gap-2"><i class="bi bi-chevron-right text-primary small"></i>System Usage</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/reports/index.blade.php ENDPATH**/ ?>