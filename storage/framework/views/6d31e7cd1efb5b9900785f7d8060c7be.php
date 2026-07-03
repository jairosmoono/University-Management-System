<?php $__env->startSection('title', 'Examinations'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Examinations</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Examinations</li>
        </ol></nav>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-exams')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamModal">
        <i class="bi bi-plus-circle me-1"></i> Schedule Exam
    </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Exam Name</th><th>Course</th><th>Date</th><th>Time</th><th>Venue</th><th>Invigilator</th><th>Max Marks</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $examinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="fw-semibold"><?php echo e($exam->name); ?></td>
                    <td><?php echo e(optional(optional($exam->courseOffering)->course)->code); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($exam->exam_date)->format('d M Y')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($exam->start_time)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($exam->end_time)->format('H:i')); ?></td>
                    <td><?php echo e($exam->venue ?? '—'); ?></td>
                    <td><?php echo e(optional(optional($exam->invigilator)->user)->name ?? '—'); ?></td>
                    <td><?php echo e($exam->max_marks); ?></td>
                    <td>
                        <?php $statusColors = ['scheduled'=>'primary','ongoing'=>'warning','completed'=>'success','cancelled'=>'danger'] ?>
                        <span class="badge bg-<?php echo e($statusColors[$exam->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($exam->status)); ?></span>
                    </td>
                    <td>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-exams')): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo e(route('academic.examinations.edit', $exam)); ?>"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('academic.examinations.seating', $exam)); ?>"><i class="bi bi-grid me-2"></i>Seating Plan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('academic.examinations.destroy', $exam)); ?>" onsubmit="return confirm('Delete?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-exams')): ?>
<div class="modal fade" id="createExamModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="<?php echo e(route('academic.examinations.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Schedule Examination</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Exam Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. CS101 Final Examination" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="">— Select Type —</option>
                                <?php $__currentLoopData = $examTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $et): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($et->code); ?>"><?php echo e($et->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Course Offering *</label>
                            <select name="course_offering_id" class="form-select" required>
                                <option value="">— Select Course —</option>
                                <?php $__currentLoopData = $offerings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($o->id); ?>"><?php echo e(optional($o->course)->code); ?> — <?php echo e(optional($o->course)->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Exam Date *</label>
                            <input type="date" name="exam_date" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Time *</label>
                            <input type="time" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Time *</label>
                            <input type="time" name="end_time" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Venue</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Main Exam Hall">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Invigilator</label>
                            <select name="invigilator_id" class="form-select">
                                <option value="">— None —</option>
                                <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>"><?php echo e(optional($s->user)->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Marks *</label>
                            <input type="number" name="max_marks" class="form-control" value="100" min="1" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pass Mark</label>
                            <input type="number" name="passing_marks" class="form-control" value="40" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/academic/examinations/index.blade.php ENDPATH**/ ?>