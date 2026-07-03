<?php $__env->startSection('title', 'Course Registrations'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            <?php if($isStudentView): ?>
                <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>My Registrations
            <?php elseif($isLecturerView): ?>
                <i class="bi bi-people-fill me-2 text-primary"></i>My Students
            <?php else: ?>
                Course Registrations
            <?php endif; ?>
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">
                <?php echo e($isStudentView ? 'My Registrations' : ($isLecturerView ? 'My Students' : 'Registrations')); ?>

            </li>
        </ol></nav>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-academic')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal" <?php echo e(!$registrationOpen && !auth()->user()->hasRole('super-admin') ? 'disabled' : ''); ?>>
        <i class="bi bi-plus-circle me-1"></i> Register Student
    </button>
    <?php endif; ?>
</div>

<?php if(!$registrationOpen): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-lock-fill fs-5"></i>
    <div>
        <strong>Course registration is currently closed.</strong>
        <?php if(auth()->user()->hasRole('super-admin')): ?>
            As a super-admin, you can still register students. Other staff cannot register or drop courses until it is reopened in Settings.
        <?php else: ?>
            New registrations and waitlist confirmations are disabled until it is reopened by an administrator.
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-bookmark-fill"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($stats['total'] ?? 0); ?></div>
                    <div class="stat-label text-muted"><?php echo e($isStudentView ? 'Total Courses' : 'Total Registrations'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($stats['registered'] ?? 0); ?></div>
                    <div class="stat-label text-muted"><?php echo e($isStudentView ? 'Active' : 'Students Registered'); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($stats['dropped'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Dropped</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-mortarboard"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($stats['completed'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Completed</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-slash-circle"></i></div>
                <div>
                    <div class="stat-value"><?php echo e($stats['failed'] ?? 0); ?></div>
                    <div class="stat-label text-muted">Failed</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <?php if($isLecturerView): ?>
            
            <div class="col-md-4">
                <select name="offering_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All My Courses</option>
                    <?php $__currentLoopData = $offeringsForFilter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($o->id); ?>" <?php echo e(request('offering_id') == $o->id ? 'selected' : ''); ?>>
                        <?php echo e(optional($o->course)->code); ?> — <?php echo e(Str::limit(optional($o->course)->title, 35)); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php elseif(!$isStudentView): ?>
            
            <div class="col-md-2">
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('semester_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($p->id); ?>" <?php echo e(request('program_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->code); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php else: ?>
            
            <div class="col-md-3">
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('semester_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="registered" <?php echo e(request('status') == 'registered' ? 'selected' : ''); ?>>Registered</option>
                    <option value="dropped" <?php echo e(request('status') == 'dropped' ? 'selected' : ''); ?>>Dropped</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
                </select>
            </div>
            <?php if(!$isStudentView): ?>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by student ID or name..." value="<?php echo e(request('search')); ?>">
            </div>
            <?php endif; ?>
            <div class="col-auto">
                <?php if(!$isStudentView): ?>
                <button class="btn btn-sm btn-primary px-3">Search</button>
                <?php endif; ?>
                <?php if(request()->hasAny(['search','status','offering_id','semester_id','program_id'])): ?>
                <a href="<?php echo e(route('academic.registrations.index')); ?>" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">

        <?php if(!$isStudentView && !$isLecturerView && $studentList): ?>
        
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Student</th>
                    <th>Programme</th>
                    <th class="text-center">Total Subjects</th>
                    <th class="text-center">Registered</th>
                    <th class="text-center">Dropped</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $studentList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:36px;height:36px;font-size:.75rem">
                                <?php echo e(strtoupper(substr(optional($student->user)->name ?? 'S', 0, 1))); ?>

                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:0.85rem"><?php echo e(optional($student->user)->name ?? '—'); ?></div>
                                <small class="text-muted"><?php echo e($student->student_id); ?></small>
                            </div>
                        </div>
                    </td>
                    <td><small class="text-muted"><?php echo e(optional($student->program)->name ?? '—'); ?></small></td>
                    <td class="text-center"><span class="badge bg-primary"><?php echo e($student->total_subjects); ?></span></td>
                    <td class="text-center"><span class="badge bg-success"><?php echo e($student->registered_count); ?></span></td>
                    <td class="text-center"><span class="badge bg-danger"><?php echo e($student->dropped_count); ?></span></td>
                    <td class="text-end pe-3">
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="viewSubjects(<?php echo e($student->id); ?>, '<?php echo e(addslashes(optional($student->user)->name)); ?>', '<?php echo e($student->student_id); ?>')">
                                <i class="bi bi-eye me-1"></i>View
                            </button>
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="editRegistrations(<?php echo e($student->id); ?>, '<?php echo e(addslashes(optional($student->user)->name)); ?>', '<?php echo e($student->student_id); ?>')">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-25"></i>No registrations found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php else: ?>
        
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <?php if(!$isStudentView): ?>
                    <th class="ps-3">Student</th>
                    <?php if($isLecturerView): ?><th>Programme</th><?php endif; ?>
                    <?php endif; ?>
                    <th class="<?php echo e($isStudentView ? 'ps-3' : ''); ?>">Course</th>
                    <th>Semester/Term</th>
                    <?php if(!$isLecturerView): ?><th>Lecturer</th><?php endif; ?>
                    <th>Status</th>
                    <th>Registered On</th>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-academic')): ?><th class="text-end pe-3">Action</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $colors = ['registered'=>'success','approved'=>'success','dropped'=>'danger','completed'=>'info','failed'=>'dark'] ?>
                <tr>
                    <?php if(!$isStudentView): ?>
                    <td class="ps-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                 style="width:36px;height:36px;font-size:.75rem">
                                <?php echo e(strtoupper(substr(optional(optional($reg->student)->user)->name ?? 'S', 0, 1))); ?>

                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:0.85rem"><?php echo e(optional(optional($reg->student)->user)->name ?? '—'); ?></div>
                                <small class="text-muted"><?php echo e(optional($reg->student)->student_id); ?></small>
                            </div>
                        </div>
                    </td>
                    <?php if($isLecturerView): ?>
                    <td><small class="text-muted"><?php echo e(optional(optional($reg->student)->program)->name ?? '—'); ?></small></td>
                    <?php endif; ?>
                    <?php endif; ?>
                    <td class="<?php echo e($isStudentView ? 'ps-3' : ''); ?>">
                        <div class="fw-semibold" style="font-size:0.85rem"><?php echo e(optional(optional($reg->courseOffering)->course)->code); ?></div>
                        <small class="text-muted"><?php echo e(Str::limit(optional(optional($reg->courseOffering)->course)->title ?? optional(optional($reg->courseOffering)->course)->name ?? '', 40)); ?></small>
                    </td>
                    <td><small><?php echo e(optional(optional($reg->courseOffering)->semester)->name ?? '—'); ?></small></td>
                    <?php if(!$isLecturerView): ?>
                    <td><small><?php echo e(optional(optional(optional($reg->courseOffering)->lecturer)->user)->name ?? '—'); ?></small></td>
                    <?php endif; ?>
                    <td><span class="badge bg-<?php echo e($colors[$reg->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($reg->status)); ?></span></td>
                    <td><small class="text-muted"><?php echo e($reg->created_at->format('d M Y')); ?></small></td>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-academic')): ?>
                    <td class="text-end pe-3">
                        <?php if($reg->status === 'registered'): ?>
                        <form method="POST" action="<?php echo e(route('academic.registrations.drop', $reg)); ?>" class="d-inline"
                              onsubmit="return confirm('Drop this course registration?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">Drop</button>
                        </form>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="99" class="text-center text-muted py-5">
                        <i class="bi bi-journal-x fs-2 d-block mb-2 opacity-25"></i>
                        <?php if($isStudentView): ?> You are not registered in any courses yet.
                        <?php elseif($isLecturerView): ?> No students found in your courses.
                        <?php else: ?> No registrations found. <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php endif; ?>

        </div>
    </div>
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">
            <?php if(!$isStudentView && !$isLecturerView && $studentList): ?>
                Showing <?php echo e($studentList->firstItem()); ?>–<?php echo e($studentList->lastItem()); ?> of <?php echo e($studentList->total()); ?> students
            <?php else: ?>
                Showing <?php echo e($registrations->firstItem()); ?>–<?php echo e($registrations->lastItem()); ?> of <?php echo e($registrations->total()); ?>

                <?php echo e($isStudentView ? 'courses' : 'registrations'); ?>

            <?php endif; ?>
        </small>
        <?php if(!$isStudentView && !$isLecturerView && $studentList): ?>
            <?php echo e($studentList->withQueryString()->links()); ?>

        <?php else: ?>
            <?php echo e($registrations->withQueryString()->links()); ?>

        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="subjectsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="subjectsModalTitle">Registered Subjects</h5>
                    <small class="text-muted" id="subjectsModalSubtitle"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="subjectsLoading" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>Loading subjects…
                </div>
                <div id="subjectsContent" class="d-none">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Course</th>
                                <th>Semester/Term</th>
                                <th>Lecturer</th>
                                <th>Status</th>
                                <th>Registered On</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editRegModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="editRegModalTitle">Edit Registrations</h5>
                    <small class="text-muted" id="editRegModalSubtitle"></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-academic')): ?>
                    <button type="button" class="btn btn-sm btn-primary" id="addSubjectBtn"
                            onclick="switchToRegisterModal()">
                        <i class="bi bi-plus-circle me-1"></i>Add Subject
                    </button>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0">
                <div id="editRegLoading" class="text-center py-4 text-muted">
                    <div class="spinner-border spinner-border-sm me-2"></div>Loading…
                </div>
                <div id="editRegContent" class="d-none">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Course</th>
                                <th>Semester/Term</th>
                                <th>Current Status</th>
                                <th>Change To</th>
                                <th class="text-end pe-3">Save</th>
                            </tr>
                        </thead>
                        <tbody id="editRegTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-academic')): ?>
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="<?php echo e(route('academic.registrations.register')); ?>" id="bulkRegForm">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Bulk Course Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Student <span class="text-danger">*</span></label>
                            <input type="text" id="reg_student_search" class="form-control" placeholder="Type student ID or name..." autocomplete="off">
                            <input type="hidden" name="student_id" id="reg_student_id">
                            <div id="studentSuggestions" class="list-group mt-1 position-absolute" style="z-index:9999;min-width:320px;max-height:200px;overflow-y:auto;"></div>
                            <div id="reg_student_badge" class="mt-1"></div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Semester/Term <span class="text-danger">*</span></label>
                            <select id="reg_semester" class="form-select" onchange="loadOfferings(this.value)">
                                <option value="">-- Select Semester/Term --</option>
                                <?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>"><?php echo e($s->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div id="offeringsSection" style="display:none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">Select Course Offerings <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <span id="selectedCount" class="badge bg-primary">0 selected</span>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-sm" onclick="toggleAll(true)">Select All</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btn-sm" onclick="toggleAll(false)">Clear</button>
                            </div>
                        </div>
                        <div id="offeringsLoading" class="text-center py-3 text-muted d-none">
                            <div class="spinner-border spinner-border-sm me-2"></div>Loading offerings...
                        </div>
                        <div id="offeringsList" class="border rounded" style="max-height:320px;overflow-y:auto;"></div>
                        <div id="offeringsEmpty" class="text-center text-muted py-3 d-none">No course offerings found for this semester/term.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <small class="text-muted me-auto" id="reg_summary"></small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="reg_submit_btn" disabled>
                        <i class="bi bi-person-check me-1"></i>Register Selected
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function loadOfferings(semesterId) {
    const section  = document.getElementById('offeringsSection');
    const loading  = document.getElementById('offeringsLoading');
    const list     = document.getElementById('offeringsList');
    const empty    = document.getElementById('offeringsEmpty');

    if (!semesterId) { section.style.display = 'none'; return; }
    section.style.display = 'block';
    loading.classList.remove('d-none');
    list.innerHTML = '';
    empty.classList.add('d-none');
    updateSelectedCount();

    const studentId = document.getElementById('reg_student_id').value;
    const studentParam = studentId ? `&student_id=${studentId}` : '';
    fetch(`/ajax/course-offerings?semester_id=${semesterId}${studentParam}`)
        .then(r => r.json())
        .then(data => {
            loading.classList.add('d-none');
            if (!data.length) { empty.classList.remove('d-none'); return; }

            list.innerHTML = data.map(o => {
                const full    = o.enrolled >= o.max;
                const pct     = o.max > 0 ? Math.round((o.enrolled / o.max) * 100) : 0;
                const barCls  = pct >= 90 ? 'bg-danger' : pct >= 70 ? 'bg-warning' : 'bg-success';
                return `
                <label class="d-flex align-items-start gap-3 px-3 py-2 border-bottom offering-row ${full ? 'opacity-50' : ''}" style="cursor:${full?'not-allowed':'pointer'}">
                    <input type="checkbox" name="course_offering_ids[]" value="${o.id}"
                           class="form-check-input mt-1 offering-cb" ${full ? 'disabled' : ''}
                           onchange="updateSelectedCount()">
                    <div class="flex-grow-1" style="min-width:0">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold">${o.course_code}</span>
                                <span class="text-muted ms-1" style="font-size:0.85rem">${o.course_name}</span>
                            </div>
                            ${full ? '<span class="badge bg-danger ms-2 flex-shrink-0">Full</span>' : ''}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div class="progress flex-grow-1" style="height:4px;max-width:120px">
                                <div class="progress-bar ${barCls}" style="width:${pct}%"></div>
                            </div>
                            <small class="text-muted">${o.enrolled}/${o.max} enrolled</small>
                            ${o.lecturer ? `<small class="text-muted">· ${o.lecturer}</small>` : ''}
                        </div>
                    </div>
                </label>`;
            }).join('');
        })
        .catch(() => {
            loading.classList.add('d-none');
            list.innerHTML = '<div class="text-danger p-3">Failed to load offerings.</div>';
        });
}

function toggleAll(check) {
    document.querySelectorAll('.offering-cb:not(:disabled)').forEach(cb => cb.checked = check);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.offering-cb:checked').length;
    document.getElementById('selectedCount').textContent = `${checked} selected`;
    document.getElementById('reg_submit_btn').disabled = checked === 0 || !document.getElementById('reg_student_id').value;
    document.getElementById('reg_summary').textContent = checked > 0
        ? `${checked} course offering${checked > 1 ? 's' : ''} will be registered`
        : '';
}

const studentSearch = document.getElementById('reg_student_search');
if (studentSearch) {
    studentSearch.addEventListener('input', function() {
        const q = this.value;
        document.getElementById('reg_student_id').value = '';
        document.getElementById('reg_student_badge').innerHTML = '';
        updateSelectedCount();
        if (q.length < 2) { document.getElementById('studentSuggestions').innerHTML = ''; return; }
        fetch(`/ajax/students?q=${q}`)
            .then(r => r.json())
            .then(data => {
                const box = document.getElementById('studentSuggestions');
                box.innerHTML = data.map(s =>
                    `<button type="button" class="list-group-item list-group-item-action py-2" onclick="selectStudent('${s.id}','${s.student_id}','${s.name}')">
                        <strong>${s.student_id}</strong> <span class="text-muted">— ${s.name}</span>
                    </button>`
                ).join('') || '<div class="list-group-item text-muted">No students found</div>';
            });
    });
}

function selectStudent(id, sid, name) {
    document.getElementById('reg_student_search').value = '';
    document.getElementById('reg_student_id').value = id;
    document.getElementById('studentSuggestions').innerHTML = '';
    document.getElementById('reg_student_badge').innerHTML =
        `<span class="badge bg-success py-2 px-3"><i class="bi bi-person-check me-1"></i>${sid} — ${name}</span>`;
    updateSelectedCount();
    // Reload offerings if a semester is already selected so filtering applies immediately
    const sem = document.getElementById('reg_semester');
    if (sem && sem.value) loadOfferings(sem.value);
}

function viewSubjects(studentId, studentName, studentNo) {
    document.getElementById('subjectsModalTitle').textContent = studentName || 'Registered Subjects';
    document.getElementById('subjectsModalSubtitle').textContent = studentNo || '';
    document.getElementById('subjectsLoading').classList.remove('d-none');
    document.getElementById('subjectsContent').classList.add('d-none');
    document.getElementById('subjectsTbody').innerHTML = '';

    const modal = new bootstrap.Modal(document.getElementById('subjectsModal'));
    modal.show();

    const statusColors = { registered: 'success', approved: 'success', dropped: 'danger', completed: 'info', failed: 'dark' };

    fetch(`<?php echo e(url('academic/registrations/students')); ?>/${studentId}/subjects`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('subjectsLoading').classList.add('d-none');
            if (!data.length) {
                document.getElementById('subjectsTbody').innerHTML =
                    '<tr><td colspan="6" class="text-center text-muted py-4">No registrations found.</td></tr>';
            } else {
                const csrf = '<?php echo e(csrf_token()); ?>';
                const dropBase = '<?php echo e(url("academic/registrations")); ?>';
                document.getElementById('subjectsTbody').innerHTML = data.map(r => {
                    const color = statusColors[r.status] || 'secondary';
                    const dropBtn = r.status === 'registered'
                        ? `<form method="POST" action="${dropBase}/${r.id}/drop" class="d-inline"
                               onsubmit="return confirm('Drop this course registration?')">
                               <input type="hidden" name="_token" value="${csrf}">
                               <input type="hidden" name="_method" value="DELETE">
                               <button class="btn btn-sm btn-outline-danger">Drop</button>
                           </form>`
                        : '—';
                    return `<tr>
                        <td class="ps-3">
                            <div class="fw-semibold" style="font-size:0.85rem">${r.course_code}</div>
                            <small class="text-muted">${r.course_name}</small>
                        </td>
                        <td><small>${r.semester}</small></td>
                        <td><small>${r.lecturer}</small></td>
                        <td><span class="badge bg-${color}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td>
                        <td><small class="text-muted">${r.registered_on}</small></td>
                        <td class="text-end pe-3">${dropBtn}</td>
                    </tr>`;
                }).join('');
            }
            document.getElementById('subjectsContent').classList.remove('d-none');
        })
        .catch(() => {
            document.getElementById('subjectsLoading').classList.add('d-none');
            document.getElementById('subjectsTbody').innerHTML =
                '<tr><td colspan="6" class="text-center text-danger py-4">Failed to load subjects.</td></tr>';
            document.getElementById('subjectsContent').classList.remove('d-none');
        });
}

let _editStudent = { id: null, name: '', no: '' };

function editRegistrations(studentId, studentName, studentNo) {
    _editStudent = { id: studentId, name: studentName, no: studentNo };
    document.getElementById('editRegModalTitle').textContent = studentName || 'Edit Registrations';
    document.getElementById('editRegModalSubtitle').textContent = studentNo || '';
    document.getElementById('editRegLoading').classList.remove('d-none');
    document.getElementById('editRegContent').classList.add('d-none');
    document.getElementById('editRegTbody').innerHTML = '';

    const modal = new bootstrap.Modal(document.getElementById('editRegModal'));
    modal.show();

    const csrf = '<?php echo e(csrf_token()); ?>';
    const statusBase = '<?php echo e(url("academic/registrations")); ?>';
    const statusOptions = ['registered','dropped','completed','failed'];
    const statusColors = { registered:'success', approved:'success', dropped:'danger', completed:'info', failed:'dark' };

    fetch(`<?php echo e(url('academic/registrations/students')); ?>/${studentId}/subjects`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('editRegLoading').classList.add('d-none');
            if (!data.length) {
                document.getElementById('editRegTbody').innerHTML =
                    '<tr><td colspan="5" class="text-center text-muted py-4">No registrations found.</td></tr>';
            } else {
                document.getElementById('editRegTbody').innerHTML = data.map(r => {
                    const color = statusColors[r.status] || 'secondary';
                    const opts = statusOptions.map(s =>
                        `<option value="${s}" ${s === r.status ? 'selected' : ''}>${s.charAt(0).toUpperCase() + s.slice(1)}</option>`
                    ).join('');
                    return `<tr id="edit-row-${r.id}">
                        <td class="ps-3">
                            <div class="fw-semibold" style="font-size:0.85rem">${r.course_code}</div>
                            <small class="text-muted">${r.course_name}</small>
                        </td>
                        <td><small>${r.semester}</small></td>
                        <td><span class="badge bg-${color}" id="edit-badge-${r.id}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td>
                        <td>
                            <select class="form-select form-select-sm" id="edit-select-${r.id}" style="min-width:120px">${opts}</select>
                        </td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-primary" onclick="saveStatus(${r.id}, '${csrf}', '${statusBase}')">
                                <i class="bi bi-check2"></i> Save
                            </button>
                        </td>
                    </tr>`;
                }).join('');
            }
            document.getElementById('editRegContent').classList.remove('d-none');
        })
        .catch(() => {
            document.getElementById('editRegLoading').classList.add('d-none');
            document.getElementById('editRegTbody').innerHTML =
                '<tr><td colspan="5" class="text-center text-danger py-4">Failed to load registrations.</td></tr>';
            document.getElementById('editRegContent').classList.remove('d-none');
        });
}

function switchToRegisterModal() {
    bootstrap.Modal.getInstance(document.getElementById('editRegModal'))?.hide();
    document.getElementById('editRegModal').addEventListener('hidden.bs.modal', function openReg() {
        this.removeEventListener('hidden.bs.modal', openReg);
        selectStudent(_editStudent.id, _editStudent.no, _editStudent.name);
        new bootstrap.Modal(document.getElementById('registerModal')).show();
    });
}

function saveStatus(regId, csrf, statusBase) {
    const select = document.getElementById(`edit-select-${regId}`);
    const newStatus = select.value;
    const btn = select.closest('tr').querySelector('button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch(`${statusBase}/${regId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: newStatus }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const statusColors = { registered:'success', approved:'success', dropped:'danger', completed:'info', failed:'dark' };
            const color = statusColors[data.status] || 'secondary';
            const badge = document.getElementById(`edit-badge-${regId}`);
            badge.className = `badge bg-${color}`;
            badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2"></i> Save';
            btn.classList.replace('btn-primary', 'btn-success');
            setTimeout(() => btn.classList.replace('btn-success', 'btn-primary'), 1500);
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2"></i> Save';
        btn.classList.replace('btn-primary', 'btn-danger');
        setTimeout(() => btn.classList.replace('btn-danger', 'btn-primary'), 1500);
    });
}

document.getElementById('registerModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('reg_student_search').value = '';
    document.getElementById('reg_student_id').value = '';
    document.getElementById('reg_student_badge').innerHTML = '';
    document.getElementById('reg_semester').value = '';
    document.getElementById('offeringsSection').style.display = 'none';
    document.getElementById('offeringsList').innerHTML = '';
    updateSelectedCount();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/academic/registrations/index.blade.php ENDPATH**/ ?>