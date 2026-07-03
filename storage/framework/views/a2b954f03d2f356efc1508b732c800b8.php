<?php $__env->startSection('title', 'Hostel Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-house-door me-2" style="color:var(--secondary)"></i>Hostel Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin|registrar|finance-officer')): ?>
            <li class="breadcrumb-item"><a href="<?php echo e(route('reports.index')); ?>">Reports</a></li>
            <?php endif; ?>
            <li class="breadcrumb-item active">Hostel</li>
        </ol></nav>
    </div>
    <a href="<?php echo e(route('reports.export', 'hostel')); ?><?php echo e(request()->getQueryString() ? '?' . request()->getQueryString() : ''); ?>"
        class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-primary"><?php echo e($hostels->count()); ?></div>
            <small class="text-muted">Hostels</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-secondary"><?php echo e(number_format($totalRooms)); ?></div>
            <small class="text-muted">Total Rooms</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-danger"><?php echo e(number_format($totalOccupied)); ?></div>
            <small class="text-muted">Occupied Beds</small>
            <?php if($overdueCount > 0): ?>
            <div><span class="badge bg-danger mt-1"><?php echo e($overdueCount); ?> overdue</span></div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <?php $rateColor = $occupancyRate >= 90 ? 'danger' : ($occupancyRate >= 70 ? 'warning' : 'success') ?>
            <div class="fw-bold fs-3 text-<?php echo e($rateColor); ?>"><?php echo e($occupancyRate); ?>%</div>
            <small class="text-muted">Occupancy Rate</small>
            <div class="progress mt-2" style="height:6px">
                <div class="progress-bar bg-<?php echo e($rateColor); ?>" style="width:<?php echo e($occupancyRate); ?>%"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-building me-2 text-primary"></i>Per-Hostel Occupancy</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Hostel</th>
                                <th>Type</th>
                                <th class="text-center">Rooms</th>
                                <th class="text-center">Capacity</th>
                                <th class="text-center">Occupied</th>
                                <th class="text-center">Available</th>
                                <th style="min-width:130px">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $gc = ['male'=>'primary','female'=>'danger','mixed'=>'success'] ?>
                            <?php $__empty_1 = true; $__currentLoopData = $hostelStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-3 fw-semibold"><?php echo e($row['hostel']->name); ?></td>
                                <td><span class="badge bg-<?php echo e($gc[$row['hostel']->type] ?? 'secondary'); ?>"><?php echo e(ucfirst($row['hostel']->type)); ?></span></td>
                                <td class="text-center"><?php echo e($row['rooms']); ?></td>
                                <td class="text-center"><?php echo e($row['capacity']); ?></td>
                                <td class="text-center text-danger fw-semibold"><?php echo e($row['occupied']); ?></td>
                                <td class="text-center text-success fw-semibold"><?php echo e($row['available']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:7px">
                                            <div class="progress-bar bg-<?php echo e($row['rate'] >= 90 ? 'danger' : ($row['rate'] >= 70 ? 'warning' : 'success')); ?>"
                                                style="width:<?php echo e($row['rate']); ?>%"></div>
                                        </div>
                                        <small class="text-muted" style="width:34px"><?php echo e($row['rate']); ?>%</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">No hostel data</td></tr>
                            <?php endif; ?>
                            <tr class="table-light fw-semibold">
                                <td class="ps-3">Total</td>
                                <td></td>
                                <td class="text-center"><?php echo e($hostelStats->sum('rooms')); ?></td>
                                <td class="text-center"><?php echo e($hostelStats->sum('capacity')); ?></td>
                                <td class="text-center text-danger"><?php echo e($hostelStats->sum('occupied')); ?></td>
                                <td class="text-center text-success"><?php echo e($hostelStats->sum('available')); ?></td>
                                <td><small class="text-muted"><?php echo e($occupancyRate); ?>% overall</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-door-open me-2 text-warning"></i>Room Types</h6>
            </div>
            <div class="card-body">
                <?php $__empty_1 = true; $__currentLoopData = $roomsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="fw-semibold text-capitalize"><?php echo e(str_replace('_', ' ', $rt->room_type)); ?></span>
                        <small class="text-muted ms-1">(<?php echo e($rt->capacity); ?> beds)</small>
                    </div>
                    <span class="badge bg-primary rounded-pill"><?php echo e($rt->rooms); ?> rooms</span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted small mb-0">No room data</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-gender-ambiguous me-2 text-info"></i>By Gender</h6>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = ['male' => 'primary', 'female' => 'danger', 'mixed' => 'success']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($byGender[$type])): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge bg-<?php echo e($color); ?> me-1"><?php echo e(ucfirst($type)); ?></span>
                        <small class="text-muted"><?php echo e($byGender[$type]['count']); ?> hostel(s)</small>
                    </div>
                    <small class="fw-semibold"><?php echo e($byGender[$type]['capacity']); ?> beds</small>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-header py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-success"></i>Monthly Allocations (Last 12 Months)</h6>
    </div>
    <div class="card-body">
        <canvas id="trendChart" height="70"></canvas>
    </div>
</div>


<div class="card border-0 shadow-sm">
    <div class="card-header py-3 border-bottom">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-list-ul me-2"></i>Allocation Records</h6>
    </div>

    
    <div class="card-body border-bottom py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Hostel</label>
                <select name="hostel_id" class="form-select form-select-sm">
                    <option value="">All Hostels</option>
                    <?php $__currentLoopData = $hostels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($h->id); ?>" <?php echo e(request('hostel_id') == $h->id ? 'selected' : ''); ?>><?php echo e($h->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active"  <?php echo e(request('status') === 'active'  ? 'selected' : ''); ?>>Active</option>
                    <option value="vacated" <?php echo e(request('status') === 'vacated' ? 'selected' : ''); ?>>Vacated</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo e(request('date_from')); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e(request('date_to')); ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="<?php echo e(route('reports.hostel')); ?>" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Student</th>
                        <th>Hostel</th>
                        <th>Room</th>
                        <th>Allocated</th>
                        <th>Expected Vacate</th>
                        <th>Actual Vacate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alloc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-3 text-muted small"><?php echo e($allocations->firstItem() + $loop->index); ?></td>
                        <td class="fw-semibold"><?php echo e(optional($alloc->student?->user)->name ?? '—'); ?></td>
                        <td><?php echo e(optional($alloc->hostelRoom?->hostel)->name ?? '—'); ?></td>
                        <td><?php echo e(optional($alloc->hostelRoom)->room_number ?? '—'); ?></td>
                        <td><?php echo e($alloc->allocation_date?->format('d M Y') ?? '—'); ?></td>
                        <td>
                            <?php if($alloc->expected_vacate_date): ?>
                                <?php $overdue = $alloc->status === 'active' && $alloc->expected_vacate_date->lt(today()) ?>
                                <span class="<?php echo e($overdue ? 'text-danger fw-semibold' : ''); ?>">
                                    <?php echo e($alloc->expected_vacate_date->format('d M Y')); ?>

                                    <?php if($overdue): ?> <i class="bi bi-exclamation-circle ms-1" title="Overdue"></i> <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($alloc->actual_vacate_date?->format('d M Y') ?? '—'); ?></td>
                        <td>
                            <?php if($alloc->status === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                            <?php else: ?>
                            <span class="badge bg-secondary">Vacated</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No allocation records match the selected filters</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">
            <?php echo e($allocations->links()); ?>

        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = <?php echo json_encode($monthlyTrend->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y')), 512) ?>;
    const data   = <?php echo json_encode($monthlyTrend->pluck('count'), 15, 512) ?>;

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'New Allocations',
                data,
                backgroundColor: 'rgba(32,201,151,0.7)',
                borderColor: 'rgba(32,201,151,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/reports/hostel.blade.php ENDPATH**/ ?>