<?php $__env->startSection('title', 'Students'); ?>
<?php $__env->startSection('page-title', 'Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-person-badge me-2" style="color:var(--secondary)"></i>Students</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i>Bulk Import
        </button>
        <button type="button" class="btn btn-outline-primary" id="headerPrintBtn" onclick="submitBulkCards()" title="Select students from the list below then click here to print their ID cards">
            <i class="bi bi-person-vcard me-1"></i>Print ID Cards
        </button>
        <a href="<?php echo e(route('students.create')); ?>" class="btn btn-primary text-white"><i class="bi bi-plus-lg me-1"></i>Add Student</a>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(session('import_errors') && count(session('import_errors'))): ?>
<div class="alert alert-warning alert-dismissible fade show">
    <strong><i class="bi bi-exclamation-circle me-1"></i>Row errors:</strong>
    <ul class="mb-0 mt-1">
        <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li style="font-size:0.85rem"><?php echo e($err); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="text" name="search" class="form-control form-control-sm" style="width:160px" placeholder="Search…" value="<?php echo e(request('search')); ?>">
            <select name="faculty_id" class="form-select form-select-sm" style="width:130px">
                <option value="">All Faculties</option>
                <?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($f->id); ?>" <?php echo e(request('faculty_id') == $f->id ? 'selected' : ''); ?>><?php echo e($f->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="program_id" class="form-select form-select-sm" style="width:140px">
                <option value="">All Programs</option>
                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>" <?php echo e(request('program_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="status" class="form-select form-select-sm" style="width:110px">
                <option value="">All Status</option>
                <?php $statusLabels = ['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended','graduated'=>'Graduated','dropped_out'=>'Dropped Out','deferred'=>'Deferred']; ?>
                <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($val); ?>" <?php echo e(request('status') == $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="text" name="sponsor" class="form-control form-control-sm" style="width:110px" placeholder="Sponsor…" value="<?php echo e(request('sponsor')); ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
            <a href="<?php echo e(route('students.index')); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
            <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-danger btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo e(route('students.export', array_merge(request()->query(), ['format' => 'pdf']))); ?>"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>PDF</a></li>
                    <li><a class="dropdown-item" href="<?php echo e(route('students.export', array_merge(request()->query(), ['format' => 'csv']))); ?>"><i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>CSV</a></li>
                </ul>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Statistics (reflect active filters) -->
<div class="row g-3 mb-3">

    
    <div class="col-6 col-md-2">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted mb-1" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.06em">Total Students</div>
                <div class="fw-bold" style="font-size:2rem;line-height:1;color:var(--bs-primary)"><?php echo e($total); ?></div>
            </div>
        </div>
    </div>

    
    <div class="col-6 col-md-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted mb-2" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.06em">By Gender</div>
                <div class="d-flex gap-3 flex-wrap">
                    <div class="text-center">
                        <div class="fw-bold fs-5 text-primary"><?php echo e($byGender->get('male', 0)); ?></div>
                        <div class="text-muted" style="font-size:0.72rem">Male</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5" style="color:#e91e8c"><?php echo e($byGender->get('female', 0)); ?></div>
                        <div class="text-muted" style="font-size:0.72rem">Female</div>
                    </div>
                    <?php $other = $byGender->filter(fn($v,$k) => !in_array($k,['male','female']))->sum(); ?>
                    <?php if($other > 0): ?>
                    <div class="text-center">
                        <div class="fw-bold fs-5 text-secondary"><?php echo e($other); ?></div>
                        <div class="text-muted" style="font-size:0.72rem">Other</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-12 col-md-7">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="text-muted mb-2" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:.06em">By Sponsor</div>
                <?php if($bySponsor->isEmpty()): ?>
                    <span class="text-muted" style="font-size:0.82rem">No data</span>
                <?php else: ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php $__currentLoopData = $bySponsor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge border fw-normal px-2 py-1"
                          style="font-size:0.78rem;background:#f0f4ff;color:#1a3a6e;border-color:#c5d0e8!important;">
                        <?php echo e($s->sponsor); ?>

                        <span class="ms-1 fw-bold"><?php echo e($s->total); ?></span>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Hint shown when Print ID Cards is clicked with nothing selected -->
<div id="cardHint" class="alert alert-warning d-none py-2 mb-2" style="font-size:0.88rem">
    <i class="bi bi-info-circle me-1"></i>
    Tick the checkboxes next to the students you want, then click <strong>Print ID Cards</strong>.
    Use the <strong>Select All</strong> checkbox in the table header to select everyone on this page.
</div>

<!-- Bulk action bar (hidden until rows selected) -->
<div id="bulkBar" class="alert alert-primary d-none d-flex align-items-center justify-content-between mb-3 py-2">
    <span><i class="bi bi-check2-square me-2"></i><strong id="selectedCount">0</strong> student(s) selected</span>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Clear</button>
        <button type="button" class="btn btn-sm btn-primary" onclick="submitBulkCards()">
            <i class="bi bi-person-vcard me-1"></i>Print ID Cards
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:36px"><input type="checkbox" id="selectAll" class="form-check-input"></th>
                        <th>Student</th>
                        <th>Student ID</th>
                        <th>Program</th>
                        <th>Faculty</th>
                        <th>Level</th>
                        <th>Sponsor</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><input type="checkbox" name="student_ids[]" value="<?php echo e($student->id); ?>" class="form-check-input row-check"></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e($student->photo_url); ?>" class="avatar rounded-circle" alt="">
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem"><?php echo e($student->full_name); ?></div>
                                    <div class="text-muted" style="font-size:0.78rem"><?php echo e($student->user?->email); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code class="text-primary"><?php echo e($student->student_id); ?></code></td>
                        <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap" title="<?php echo e($student->program?->name); ?>"><?php echo e($student->program?->name); ?></td>
                        <td><?php echo e(optional($student->program?->department?->faculty)->code ?? '—'); ?></td>
                        <td><span class="badge bg-light text-dark border">Yr <?php echo e($student->year_of_study); ?></span></td>
                        <td style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap"
                            title="<?php echo e($student->sponsor); ?>"><?php echo e($student->sponsor ?? '—'); ?></td>
                        <td><?php echo statusBadge($student->status); ?></td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?php echo e(route('students.show', $student)); ?>"><i class="bi bi-eye me-2 text-primary"></i>View Profile</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('students.edit', $student)); ?>"><i class="bi bi-pencil me-2 text-secondary"></i>Edit</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('students.card', $student)); ?>"><i class="bi bi-person-vcard me-2 text-secondary"></i>ID Card</a></li>
                                    <li><a class="dropdown-item" href="<?php echo e(route('students.transcript', $student)); ?>"><i class="bi bi-file-earmark-pdf me-2 text-secondary"></i>Transcript</a></li>
                                    <li><hr class="dropdown-divider"></li>

                                    <?php
                                        $st = $student->status;
                                    ?>

                                    
                                    <?php if($st !== 'active'): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Reinstate <?php echo e(addslashes($student->full_name)); ?> as active?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="active">
                                            <button class="dropdown-item text-success">
                                                <i class="bi bi-person-check me-2"></i>Reinstate
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if($st === 'active'): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Suspend <?php echo e(addslashes($student->full_name)); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="suspended">
                                            <button class="dropdown-item text-warning">
                                                <i class="bi bi-slash-circle me-2"></i>Suspend
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if(in_array($st, ['active', 'suspended'])): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Defer enrollment for <?php echo e(addslashes($student->full_name)); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="deferred">
                                            <button class="dropdown-item text-info">
                                                <i class="bi bi-pause-circle me-2"></i>Defer Enrollment
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if($st === 'active'): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Mark <?php echo e(addslashes($student->full_name)); ?> as graduated?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="graduated">
                                            <button class="dropdown-item text-primary">
                                                <i class="bi bi-mortarboard me-2"></i>Graduate
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if($st === 'active'): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Mark <?php echo e(addslashes($student->full_name)); ?> as inactive?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="inactive">
                                            <button class="dropdown-item text-secondary">
                                                <i class="bi bi-person-dash me-2"></i>Mark Inactive
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if($st !== 'dropped_out'): ?>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.status', $student)); ?>"
                                              onsubmit="return confirm('Mark <?php echo e(addslashes($student->full_name)); ?> as Dropped Out?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="dropped_out">
                                            <button class="dropdown-item text-danger">
                                                <i class="bi bi-person-x me-2"></i>Drop Out
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                    
                                    <?php if($st === 'graduated'): ?>
                                    <li><span class="dropdown-item text-muted" style="font-size:0.82rem;cursor:default">
                                        <i class="bi bi-mortarboard me-2"></i>Already Graduated
                                    </span></li>
                                    <?php endif; ?>

                                    <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="<?php echo e(route('students.destroy', $student)); ?>"
                                              onsubmit="return confirm('Permanently delete <?php echo e(addslashes($student->full_name)); ?>? All records will be erased and this cannot be undone.')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i>Delete Permanently
                                            </button>
                                        </form>
                                    </li>
                                    <?php endif; ?>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-person-badge fs-1 d-block mb-2 opacity-25"></i>No students found
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($students->hasPages()): ?>
    <div class="card-footer d-flex align-items-center justify-content-between py-2">
        <div class="text-muted" style="font-size:0.85rem">Showing <?php echo e($students->firstItem()); ?>-<?php echo e($students->lastItem()); ?> of <?php echo e($students->total()); ?></div>
        <?php echo e($students->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- Bulk Import Modal -->
<?php if(auth()->guard()->check()): ?>
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Bulk Import Students</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('students.bulk-import')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">

                    <!-- Format guide -->
                    <div class="alert alert-info d-flex gap-3 align-items-start py-2">
                        <i class="bi bi-info-circle-fill mt-1 flex-shrink-0"></i>
                        <div style="font-size:0.85rem">
                            <strong>Accepted formats:</strong> CSV, Excel (.xlsx / .xls), XML<br>
                            <strong>Required columns:</strong>
                            <code>first_name</code>, <code>last_name</code>, <code>email</code>,
                            <code>date_of_birth</code>, <code>gender</code>, <code>program_code</code><br>
                            <strong>Optional columns:</strong>
                            <code>middle_name</code>, <code>phone</code>, <code>national_id</code>,
                            <code>nationality</code>, <code>address</code>, <code>sponsor</code>,
                            <code>year_of_study</code>, <code>enrollment_date</code>, <code>admission_type</code>,
                            <code>password</code>, <code>emergency_contact_name</code>, <code>emergency_contact_phone</code><br>
                            <strong>Notes:</strong>
                            Gender: <code>male</code>, <code>female</code>, or <code>other</code>.
                            Dates: <code>YYYY-MM-DD</code>.
                            Admission type: <code>full-time</code>, <code>part-time</code>, <code>distance</code>, or <code>online</code>.
                            Default password: <code>Student@123</code>.<br>
                            <strong class="text-danger">⚠ If a field contains a comma (e.g. an address like "Plot 12, Lusaka"), wrap it in double-quotes in your CSV: <code>"Plot 12, Lusaka"</code>. The downloaded template does this automatically.</strong>
                        </div>
                    </div>

                    <!-- Program code reference -->
                    <div class="mb-3">
                        <p class="mb-1 fw-semibold" style="font-size:0.85rem">
                            Available Program Codes
                            <span class="text-muted fw-normal">(use any of these in the <code>program_code</code> column)</span>
                        </p>
                        <table class="table table-sm table-bordered mb-0" style="font-size:0.8rem">
                            <thead class="table-light">
                                <tr><th>Code</th><th>Program Name</th></tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = \App\Models\Program::active()->orderBy('code')->get(['code','name']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><code><?php echo e($prog->code); ?></code></td>
                                    <td><?php echo e($prog->name); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Template download -->
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <a href="<?php echo e(route('students.import-template')); ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download me-1"></i>Download CSV Template
                        </a>
                        <small class="text-muted">Use this as a starting point for your import file.</small>
                    </div>

                    <!-- File input -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Select File *</label>
                        <input type="file" name="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               accept=".csv,.xlsx,.xls,.xml" required>
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Max file size: 10 MB</div>
                    </div>

                    <!-- XML format hint (collapsed) -->
                    <div>
                        <a class="small text-muted" data-bs-toggle="collapse" href="#xmlHint">XML format reference</a>
                        <div class="collapse mt-2" id="xmlHint">
                            <pre class="bg-light rounded p-2" style="font-size:0.78rem">&lt;students&gt;
  &lt;student&gt;
    &lt;!-- Required --&gt;
    &lt;first_name&gt;Jane&lt;/first_name&gt;
    &lt;last_name&gt;Banda&lt;/last_name&gt;
    &lt;email&gt;jane@example.com&lt;/email&gt;
    &lt;date_of_birth&gt;2000-05-14&lt;/date_of_birth&gt;
    &lt;gender&gt;female&lt;/gender&gt;
    &lt;program_code&gt;BSCS&lt;/program_code&gt;
    &lt;!-- Optional --&gt;
    &lt;middle_name&gt;Mary&lt;/middle_name&gt;
    &lt;phone&gt;+260971000001&lt;/phone&gt;
    &lt;nrc_number&gt;123456/78/1&lt;/nrc_number&gt;
    &lt;nationality&gt;Zambian&lt;/nationality&gt;
    &lt;address&gt;Plot 12, Lusaka&lt;/address&gt;
    &lt;sponsor&gt;Self&lt;/sponsor&gt;
    &lt;year_of_study&gt;1&lt;/year_of_study&gt;
    &lt;enrollment_date&gt;2024-09-01&lt;/enrollment_date&gt;
    &lt;admission_type&gt;full-time&lt;/admission_type&gt;
    &lt;password&gt;Student@123&lt;/password&gt;
    &lt;emergency_contact_name&gt;John Banda&lt;/emergency_contact_name&gt;
    &lt;emergency_contact_phone&gt;+260977000002&lt;/emergency_contact_phone&gt;
  &lt;/student&gt;
&lt;/students&gt;</pre>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i>Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->startPush('scripts'); ?>
<script>
const checkboxes  = () => document.querySelectorAll('.row-check');
const bulkBar     = document.getElementById('bulkBar');
const countEl     = document.getElementById('selectedCount');
const selectAllCb = document.getElementById('selectAll');
const hintEl      = document.getElementById('cardHint');

function updateBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    countEl.textContent = checked;
    bulkBar.classList.toggle('d-none', checked === 0);
    bulkBar.classList.toggle('d-flex', checked > 0);
    if (hintEl) hintEl.classList.toggle('d-none', checked > 0);
}

function clearSelection() {
    checkboxes().forEach(cb => cb.checked = false);
    if (selectAllCb) selectAllCb.checked = false;
    updateBar();
}

function submitBulkCards() {
    const selected = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        if (hintEl) {
            hintEl.classList.remove('d-none');
            hintEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route('students.bulk-cards')); ?>';
    form.style.display = 'none';

    const csrf = document.createElement('input');
    csrf.type  = 'hidden';
    csrf.name  = '_token';
    csrf.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrf);

    selected.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'student_ids[]';
        inp.value = id;
        form.appendChild(inp);
    });

    document.body.appendChild(form);
    form.submit();
}

if (selectAllCb) {
    selectAllCb.addEventListener('change', function () {
        checkboxes().forEach(cb => cb.checked = this.checked);
        updateBar();
    });
}

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-check')) {
        if (!e.target.checked && selectAllCb) selectAllCb.checked = false;
        updateBar();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/students/index.blade.php ENDPATH**/ ?>