<?php $__env->startSection('title', 'System Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-4">
    <h4 class="mb-1">System Settings</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol></nav>
</div>

<ul class="nav nav-tabs mb-4" id="settingsTabs">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#general">General</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#academic-settings">Academic</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#email-settings">Email</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#backup-tab">Backup</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#audit-tab">Audit Log</a></li>
</ul>

<div class="tab-content">
    <!-- General Settings -->
    <div class="tab-pane fade show active" id="general">

        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Branding — Logo & Favicon</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.settings.upload-branding')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="row g-4 align-items-start">

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">University Logo</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if(!empty($settings['logo_path'])): ?>
                                    <img src="<?php echo e(asset('storage/' . $settings['logo_path'])); ?>" alt="Logo" class="border rounded p-1" style="height:64px;max-width:180px;object-fit:contain;">
                                    <span class="badge bg-success">Uploaded</span>
                                <?php else: ?>
                                    <div class="border rounded d-flex align-items-center justify-content-center bg-light" style="height:64px;width:140px">
                                        <i class="bi bi-image text-muted fs-3"></i>
                                    </div>
                                    <span class="badge bg-secondary">Not set</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.svg,.webp">
                            <div class="form-text">PNG, JPG, SVG or WebP — max 2 MB. Recommended: 300×80 px, transparent background.</div>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Favicon</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <?php if(!empty($settings['favicon_path'])): ?>
                                    <img src="<?php echo e(asset('storage/' . $settings['favicon_path'])); ?>" alt="Favicon" class="border rounded p-1" style="height:40px;width:40px;object-fit:contain;">
                                    <span class="badge bg-success">Uploaded</span>
                                <?php else: ?>
                                    <div class="border rounded d-flex align-items-center justify-content-center bg-light" style="height:40px;width:40px">
                                        <i class="bi bi-grid text-muted"></i>
                                    </div>
                                    <span class="badge bg-secondary">Not set</span>
                                <?php endif; ?>
                            </div>
                            <input type="file" name="favicon" class="form-control" accept=".png,.ico,.svg">
                            <div class="form-text">PNG, ICO or SVG — max 512 KB. Recommended: 32×32 px.</div>
                        </div>

                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload Branding</button>
                        <?php if(!empty($settings['logo_path']) || !empty($settings['favicon_path'])): ?>
                            <small class="text-muted ms-3">Uploading a new file replaces the existing one.</small>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">Landing Page Hero — Slideshow Images</h6>
                <small class="text-muted">JPG / PNG / WebP &bull; max 5 MB each &bull; Recommended: 1920×1080 px</small>
            </div>
            <div class="card-body">

                
                <?php $heroImages = $settings['hero_images'] ?? []; ?>
                <?php if(count($heroImages)): ?>
                <div class="row g-3 mb-4" id="heroImageGrid">
                    <?php $__currentLoopData = $heroImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-4 col-lg-3 hero-img-item">
                        <div class="position-relative rounded overflow-hidden border" style="aspect-ratio:16/9;background:#000">
                            <img src="<?php echo e(asset('storage/' . $img)); ?>" alt="Hero slide"
                                 style="width:100%;height:100%;object-fit:cover;opacity:.9">
                            <form method="POST" action="<?php echo e(route('admin.settings.hero-images.delete')); ?>"
                                  class="position-absolute top-0 end-0 m-1"
                                  onsubmit="return confirm('Remove this slide image?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <input type="hidden" name="path" value="<?php echo e($img); ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                        style="width:28px;height:28px;padding:0;line-height:1;border-radius:6px">
                                    <i class="bi bi-x-lg" style="font-size:.7rem"></i>
                                </button>
                            </form>
                            <div class="position-absolute bottom-0 start-0 end-0 px-2 py-1"
                                 style="background:rgba(0,0,0,.5);font-size:.72rem;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                <?php echo e(basename($img)); ?>

                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php else: ?>
                <div class="text-muted small mb-3">
                    <i class="bi bi-images me-1"></i>No hero images uploaded yet — the landing page will use the default gradient slides.
                </div>
                <?php endif; ?>

                
                <form method="POST" action="<?php echo e(route('admin.settings.hero-images.upload')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="d-flex align-items-end gap-3 flex-wrap">
                        <div class="flex-grow-1" style="min-width:260px">
                            <label class="form-label fw-semibold">Add Slide Images</label>
                            <input type="file" name="hero_images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple required>
                            <div class="form-text">You can select multiple files at once. Images display in upload order.</div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>Upload Images
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <form method="POST" action="<?php echo e(route('admin.settings.update')); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <input type="hidden" name="group" value="general">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">University Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">University Name *</label>
                            <input type="text" name="university_name" class="form-control" value="<?php echo e($settings['university_name'] ?? config('university.name')); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Short Name / Abbreviation</label>
                            <input type="text" name="university_short_name" class="form-control" value="<?php echo e($settings['university_short_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="university_email" class="form-control" value="<?php echo e($settings['university_email'] ?? config('university.email')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="university_phone" class="form-control" value="<?php echo e($settings['university_phone'] ?? config('university.phone')); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="university_address" class="form-control" rows="2"><?php echo e($settings['university_address'] ?? config('university.address')); ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="university_city" class="form-control" value="<?php echo e($settings['university_city'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Country</label>
                            <input type="text" name="university_country" class="form-control" value="<?php echo e($settings['university_country'] ?? 'Zambia'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Website</label>
                            <input type="url" name="university_website" class="form-control" value="<?php echo e($settings['university_website'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">System Preferences</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" value="<?php echo e($settings['currency_symbol'] ?? 'K'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Currency Code</label>
                            <input type="text" name="currency_code" class="form-control" value="<?php echo e($settings['currency_code'] ?? 'ZMW'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Library Fine per Day</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="library_fine_rate" class="form-control" value="<?php echo e($settings['library_fine_rate'] ?? '5'); ?>" min="0" step="0.5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode" value="1" <?php echo e(($settings['maintenance_mode'] ?? false) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="maintenanceMode">Maintenance Mode</label>
                            </div>
                            <div class="form-text">When enabled, all users except super-admins are shown a maintenance page and cannot use the system.</div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="registration_open" id="regOpen" value="1" <?php echo e(($settings['registration_open'] ?? true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="regOpen">Student Registration Open</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="admissions_open" id="admissionsOpen" value="1" <?php echo e(($settings['admissions_open'] ?? true) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="admissionsOpen">Online Admission Applications Open</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Save Settings</button>
        </form>
    </div>

    <!-- Academic Settings Tab -->
    <div class="tab-pane fade" id="academic-settings">

        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Current Academic Year</h6>
                        <a href="<?php echo e(route('academic.academic-years.index')); ?>" class="btn btn-sm btn-outline-primary">Manage All</a>
                    </div>
                    <div class="card-body">
                        <?php if($currentAcademicYear): ?>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-calendar3 text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold"><?php echo e($currentAcademicYear->name); ?></h5>
                                    <small class="text-muted"><?php echo e($currentAcademicYear->start_date->format('d M Y')); ?> – <?php echo e($currentAcademicYear->end_date->format('d M Y')); ?></small>
                                </div>
                            </div>
                            <span class="badge bg-success">Active</span>
                            <span class="badge bg-light text-dark ms-1"><?php echo e($currentAcademicYear->semesters->count()); ?> semester(s)/term(s)</span>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-calendar-x display-5"></i>
                                <p class="mt-2 mb-0">No active academic year set.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Current Semester/Term</h6>
                        <a href="<?php echo e(route('academic.semesters.index')); ?>" class="btn btn-sm btn-outline-primary">Manage All</a>
                    </div>
                    <div class="card-body">
                        <?php if($currentSemester): ?>
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-3 bg-warning bg-opacity-10 p-3">
                                    <i class="bi bi-journal-bookmark text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold"><?php echo e($currentSemester->name); ?></h5>
                                    <small class="text-muted"><?php echo e(optional($currentSemester->academicYear)->name); ?></small>
                                </div>
                            </div>
                            <div class="row g-2 text-center mb-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Start</small>
                                    <span class="fw-semibold small"><?php echo e($currentSemester->start_date->format('d M Y')); ?></span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">End</small>
                                    <span class="fw-semibold small"><?php echo e($currentSemester->end_date->format('d M Y')); ?></span>
                                </div>
                            </div>
                            <?php
                                $statusColors = ['upcoming'=>'secondary','registration'=>'info','active'=>'success','exam'=>'warning','completed'=>'dark'];
                            ?>
                            <span class="badge bg-<?php echo e($statusColors[$currentSemester->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($currentSemester->status)); ?></span>
                            <?php if($currentSemester->isRegistrationOpen()): ?>
                                <span class="badge bg-info ms-1">Registration Open</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-3">
                                <i class="bi bi-journal-x display-5"></i>
                                <p class="mt-2 mb-0">No active semester/term set.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Academic Years</h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAcademicYearModal">
                    <i class="bi bi-plus-circle me-1"></i>Add Academic Year
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Period</th><th>Semesters/Terms</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($ay->is_current ? 'table-success' : ''); ?>">
                            <td class="fw-semibold">
                                <?php echo e($ay->name); ?>

                                <?php if($ay->is_current): ?><span class="badge bg-success ms-1">Current</span><?php endif; ?>
                            </td>
                            <td><small><?php echo e($ay->start_date->format('M Y')); ?> – <?php echo e($ay->end_date->format('M Y')); ?></small></td>
                            <td>
                                <?php $__currentLoopData = $ay->semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-<?php echo e($statusColors[$sem->status] ?? 'secondary'); ?> me-1">
                                        <?php echo e($sem->name); ?><?php echo e($sem->is_current ? ' ★' : ''); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($ay->semesters->isEmpty()): ?><span class="text-muted small">none</span><?php endif; ?>
                            </td>
                            <td>
                                <?php $ayColors = ['upcoming'=>'secondary','active'=>'success','completed'=>'dark']; ?>
                                <span class="badge bg-<?php echo e($ayColors[$ay->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($ay->status)); ?></span>
                            </td>
                            <td>
                                <?php if(!$ay->is_current): ?>
                                <form method="POST" action="<?php echo e(route('academic.academic-years.set-current', $ay)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn btn-xs btn-outline-success btn-sm" title="Set as Current">
                                        <i class="bi bi-check-circle me-1"></i>Set Current
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addSemesterModal<?php echo e($ay->id); ?>" title="Add Semester/Term">
                                    <i class="bi bi-plus"></i> Semester/Term
                                </button>
                                <a href="<?php echo e(route('academic.academic-years.edit', $ay)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No academic years found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Course Types</h6>
                <small class="text-muted">Manage the types available when creating courses.</small>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php $__empty_1 = true; $__currentLoopData = $courseTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 d-inline-flex align-items-center gap-1 px-3 py-2 fs-6">
                        <?php echo e(ucfirst($ct)); ?>

                        <form method="POST" action="<?php echo e(route('admin.settings.course-types.destroy', $ct)); ?>" class="d-inline" onsubmit="return confirm('Remove course type \'<?php echo e($ct); ?>\'?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-close ms-1" style="width:0.6em;height:0.6em;" title="Remove"></button>
                        </form>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted mb-0 small">No course types defined yet.</p>
                    <?php endif; ?>
                </div>
                <?php if(session('success') && str_contains(session('success'), 'Course type')): ?>
                <div class="alert alert-success py-2 small"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('admin.settings.course-types.store')); ?>" class="d-flex gap-2 align-items-start" style="max-width:380px">
                    <?php echo csrf_field(); ?>
                    <div class="flex-grow-1">
                        <input type="text" name="course_type" class="form-control form-control-sm <?php $__errorArgs = ['course_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="e.g. seminar" pattern="[a-zA-Z0-9_ \-]+" title="Letters, numbers, spaces, hyphens only">
                        <?php $__errorArgs = ['course_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle me-1"></i>Add</button>
                </form>
                <div class="form-text mt-1">These types appear in the Course Type dropdown when creating or editing a course.</div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">All Semesters/Terms</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Semester/Term</th><th>Academic Year</th><th>Period</th><th>Registration</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $ay->semesters->sortBy('start_date'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($sem->is_current ? 'table-warning' : ''); ?>">
                                <td class="fw-semibold">
                                    <?php echo e($sem->name); ?>

                                    <?php if($sem->is_current): ?><span class="badge bg-warning text-dark ms-1">Current</span><?php endif; ?>
                                </td>
                                <td><small><?php echo e($ay->name); ?></small></td>
                                <td><small><?php echo e($sem->start_date->format('d M Y')); ?> – <?php echo e($sem->end_date->format('d M Y')); ?></small></td>
                                <td>
                                    <?php if($sem->registration_start && $sem->registration_end): ?>
                                        <small><?php echo e($sem->registration_start->format('d M')); ?> – <?php echo e($sem->registration_end->format('d M Y')); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted">—</small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-<?php echo e($statusColors[$sem->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($sem->status)); ?></span></td>
                                <td>
                                    <?php if(!$sem->is_current): ?>
                                    <form method="POST" action="<?php echo e(route('academic.semesters.set-current', $sem)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-outline-warning" title="Set as Current">
                                            <i class="bi bi-check-circle me-1"></i>Set Current
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('academic.semesters.edit', $sem)); ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($academicYears->every(fn($ay) => $ay->semesters->isEmpty())): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No semesters/terms found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Email Settings Tab -->
    <div class="tab-pane fade" id="email-settings">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold">Email Configuration</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Email settings are configured in <code>.env</code> — set <code>MAIL_HOST</code>, <code>MAIL_USERNAME</code>, <code>MAIL_PASSWORD</code>, and <code>MAIL_FROM_ADDRESS</code> there.
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4"><label class="form-label text-muted small">Driver</label><p class="fw-semibold"><?php echo e(config('mail.default')); ?></p></div>
                    <div class="col-md-4"><label class="form-label text-muted small">Host</label><p class="fw-semibold"><?php echo e(config('mail.mailers.smtp.host', '—')); ?></p></div>
                    <div class="col-md-4"><label class="form-label text-muted small">Port</label><p class="fw-semibold"><?php echo e(config('mail.mailers.smtp.port', '—')); ?></p></div>
                    <div class="col-md-6"><label class="form-label text-muted small">From Address</label><p class="fw-semibold"><?php echo e(config('mail.from.address', '—')); ?></p></div>
                    <div class="col-md-6"><label class="form-label text-muted small">From Name</label><p class="fw-semibold"><?php echo e(config('mail.from.name', '—')); ?></p></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Tab -->
    <div class="tab-pane fade" id="backup-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Database Backup</h6>
                <p class="text-muted">Create a complete backup of the database. The backup file will be downloaded as a SQL file.</p>
                <form method="POST" action="<?php echo e(route('admin.backup.create')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> Create & Download Backup
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Audit Log Tab -->
    <div class="tab-pane fade" id="audit-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date/Time</th><th>User</th><th>Action</th><th>Model</th><th>URL</th><th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><small><?php echo e(\Carbon\Carbon::parse($log->created_at)->format('d M Y H:i')); ?></small></td>
                            <td><?php echo e(optional($log->user)->name ?? 'System'); ?></td>
                            <td><span class="badge bg-<?php echo e(in_array($log->action, ['POST','PUT','PATCH']) ? 'warning' : ($log->action === 'DELETE' ? 'danger' : 'secondary')); ?>"><?php echo e($log->action); ?></span></td>
                            <td><small class="text-muted"><?php echo e($log->model_type); ?></small></td>
                            <td><small class="text-truncate" style="max-width:200px"><?php echo e($log->url); ?></small></td>
                            <td><code class="small"><?php echo e($log->ip_address); ?></code></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo e($auditLogs->links()); ?>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addAcademicYearModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="<?php echo e(route('academic.academic-years.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Academic Year</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. 2025/2026" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">End Date *</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming">Upcoming</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="isCurrentAY">
                        <label class="form-check-label" for="isCurrentAY">Set as current academic year</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>


<?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="addSemesterModal<?php echo e($ay->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="<?php echo e(route('academic.semesters.store')); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="academic_year_id" value="<?php echo e($ay->id); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Semester/Term — <?php echo e($ay->name); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Semester/Term Name *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Semester 1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming">Upcoming</option>
                                <option value="registration">Registration</option>
                                <option value="active">Active</option>
                                <option value="exam">Exam</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date *</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration Start</label>
                            <input type="date" name="registration_start" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration End</label>
                            <input type="date" name="registration_end" class="form-control">
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="isCurrentSem<?php echo e($ay->id); ?>">
                        <label class="form-check-label" for="isCurrentSem<?php echo e($ay->id); ?>">Set as current semester/term</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Semester/Term</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>