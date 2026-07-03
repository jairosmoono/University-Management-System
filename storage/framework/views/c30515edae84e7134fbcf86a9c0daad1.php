<?php $__env->startSection('title', 'Edit Student'); ?>
<?php $__env->startSection('page-title', 'Edit Student'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2" style="color:var(--secondary)"></i>Edit Student</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('students.index')); ?>">Students</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('students.show', $student)); ?>"><?php echo e($student->student_id); ?></a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol></nav>
</div>

<form method="POST" action="<?php echo e(route('students.update', $student)); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="row g-3">
        <!-- Personal Info -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php
                            $nameParts  = explode(' ', $student->user->name ?? '', 3);
                            $firstName  = old('first_name',  $nameParts[0] ?? '');
                            $middleName = old('middle_name', isset($nameParts[2]) ? $nameParts[1] : '');
                            $lastName   = old('last_name',   isset($nameParts[2]) ? $nameParts[2] : ($nameParts[1] ?? ''));
                        ?>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e($firstName); ?>" required>
                            <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="<?php echo e($middleName); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e($lastName); ?>" required>
                            <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('email', $student->user->email)); ?>" required>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="<?php echo e(old('phone', $student->phone)); ?>" placeholder="+260 XXX XXXXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="<?php echo e(old('date_of_birth', $student->date_of_birth?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select...</option>
                                <?php $__currentLoopData = ['male','female','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($g); ?>" <?php echo e(old('gender', $student->gender) == $g ? 'selected' : ''); ?>><?php echo e(ucfirst($g)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">National ID</label>
                            <input type="text" name="national_id" class="form-control"
                                value="<?php echo e(old('national_id', $student->national_id)); ?>" placeholder="000000/00/0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nationality</label>
                            <input type="text" name="nationality" class="form-control"
                                value="<?php echo e(old('nationality', $student->nationality)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sponsor / Funding Source</label>
                            <input type="text" name="sponsor" class="form-control"
                                value="<?php echo e(old('sponsor', $student->sponsor)); ?>"
                                placeholder="e.g. Self, Government Bursary, NGO…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $student->address)); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Info -->
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Faculty</label>
                            <select name="faculty_id" id="faculty_id" class="form-select" onchange="loadDepartments(this.value)">
                                <option value="">All Faculties...</option>
                                <?php $__currentLoopData = $faculties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($f->id); ?>"
                                    <?php echo e(old('faculty_id', $student->program?->department?->faculty_id) == $f->id ? 'selected' : ''); ?>>
                                    <?php echo e($f->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="department_id" class="form-select" onchange="loadPrograms(this.value)">
                                <option value="">All Departments...</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>"
                                    <?php echo e(old('department_id', $student->program?->department_id) == $d->id ? 'selected' : ''); ?>>
                                    <?php echo e($d->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                            <select name="program_id" id="program_id" class="form-select <?php $__errorArgs = ['program_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Select Program...</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php echo e(old('program_id', $student->program_id) == $p->id ? 'selected' : ''); ?>>
                                    <?php echo e($p->name); ?> (<?php echo e($p->code); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['program_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Type</label>
                            <select name="admission_type" class="form-select">
                                <option value="full-time" <?php echo e(old('admission_type', $student->admission_type) == 'full-time' ? 'selected' : ''); ?>>Full-Time</option>
                                <option value="part-time" <?php echo e(old('admission_type', $student->admission_type) == 'part-time' ? 'selected' : ''); ?>>Part-Time</option>
                                <option value="distance"  <?php echo e(old('admission_type', $student->admission_type) == 'distance'  ? 'selected' : ''); ?>>Distance</option>
                                <option value="online"    <?php echo e(old('admission_type', $student->admission_type) == 'online'    ? 'selected' : ''); ?>>Online</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year of Study</label>
                            <select name="year_of_study" class="form-select">
                                <?php for($y = 1; $y <= 6; $y++): ?>
                                <option value="<?php echo e($y); ?>" <?php echo e(old('year_of_study', $student->year_of_study) == $y ? 'selected' : ''); ?>>Year <?php echo e($y); ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Enrollment Date</label>
                            <input type="date" name="enrollment_date" class="form-control"
                                value="<?php echo e(old('enrollment_date', $student->enrollment_date?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expected Graduation</label>
                            <input type="date" name="expected_graduation" class="form-control"
                                value="<?php echo e(old('expected_graduation', $student->expected_graduation?->format('Y-m-d'))); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <?php $statusLabels = ['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended','deferred'=>'Deferred','graduated'=>'Graduated','dropped_out'=>'Dropped Out']; ?>
                                <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('status', $student->status) == $val ? 'selected' : ''); ?>><?php echo e($lbl); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-image me-2"></i>Profile Photo</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mx-auto mb-3 rounded overflow-hidden"
                         style="width:140px;height:175px;background:#f0f4f8;border:2px dashed #dee2e6">
                        <img id="photoPreview" src="<?php echo e($student->photo_url); ?>"
                             style="width:100%;height:100%;object-fit:cover" alt="Photo">
                    </div>
                    <input type="file" name="photo" id="photo" class="d-none" accept="image/*"
                        onchange="previewPhoto(this)">
                    <label for="photo" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-camera me-1"></i>Change Photo
                    </label>
                    <div class="text-muted mt-1" style="font-size:0.75rem">JPG, PNG. Max 2MB</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-key me-2"></i>Reset Password</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="Leave blank to keep current">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            <?php $emergencyContact = $student->guardians->where('is_emergency_contact', true)->first(); ?>
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h6>
                </div>
                <div class="card-body">
                    <?php if($emergencyContact): ?>
                    <div class="mb-2 p-2 bg-light rounded" style="font-size:0.85rem">
                        <div class="fw-semibold"><?php echo e($emergencyContact->name); ?></div>
                        <div class="text-muted"><?php echo e($emergencyContact->phone); ?></div>
                        <div class="text-muted"><?php echo e(ucfirst($emergencyContact->relationship)); ?></div>
                    </div>
                    <a href="<?php echo e(route('students.show', $student)); ?>#guardian"
                       class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-people me-1"></i>Manage Guardians
                    </a>
                    <?php else: ?>
                    <p class="text-muted small mb-2">No emergency contact on record.</p>
                    <a href="<?php echo e(route('students.show', $student)); ?>#guardian"
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-person-plus me-1"></i>Add Guardian
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary text-white">
                    <i class="bi bi-save me-1"></i>Save Changes
                </button>
                <a href="<?php echo e(route('students.show', $student)); ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
function loadDepartments(facultyId) {
    if (!facultyId) return;
    fetch(`/ajax/departments/by-faculty/${facultyId}`)
        .then(r => r.json()).then(data => {
            const sel = document.getElementById('department_id');
            sel.innerHTML = '<option value="">All Departments...</option>';
            data.forEach(d => sel.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            document.getElementById('program_id').innerHTML = '<option value="">Select Program...</option>';
        });
}
function loadPrograms(deptId) {
    if (!deptId) return;
    fetch(`/ajax/programs/by-department/${deptId}`)
        .then(r => r.json()).then(data => {
            const sel = document.getElementById('program_id');
            sel.innerHTML = '<option value="">Select Program...</option>';
            data.forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name} (${p.code})</option>`);
        });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/students/edit.blade.php ENDPATH**/ ?>