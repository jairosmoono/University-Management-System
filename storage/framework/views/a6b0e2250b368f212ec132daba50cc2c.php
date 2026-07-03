<?php $__env->startSection('title', 'Email Notifications'); ?>
<?php $__env->startSection('page-title', 'Email Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="bi bi-envelope-paper me-2" style="color:var(--secondary)"></i>Email Notifications</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Email Notifications</li>
        </ol></nav>
    </div>
</div>

<?php if(session('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if(session('warning')): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i><?php echo e(session('warning')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-pencil-square me-2"></i>Compose Email</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('admin.email-notifications.send')); ?>" method="POST" id="composeForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipients</label>
                        <select name="recipient_type" id="recipientType" class="form-select" onchange="toggleRecipientOptions(this.value)">
                            <option value="all">All Active Users</option>
                            <option value="students">All Students</option>
                            <option value="staff">All Staff</option>
                            <option value="department">By Department</option>
                            <option value="role">By Role</option>
                            <option value="individual">Specific Users (by ID)</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="deptOption">
                        <label class="form-label fw-semibold">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">Select department…</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="roleOption">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" class="form-select">
                            <option value="">Select role…</option>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->name); ?>"><?php echo e(ucfirst(str_replace('-', ' ', $role->name))); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="individualOption">
                        <label class="form-label fw-semibold">User IDs <small class="text-muted">(comma-separated)</small></label>
                        <input type="text" name="user_ids" class="form-control" placeholder="e.g. 5, 12, 34">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('subject')); ?>" placeholder="Email subject…" required>
                        <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message Body</label>
                        <textarea name="body" rows="5" class="form-control <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Write your message here…" required><?php echo e(old('body')); ?></textarea>
                        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachments <small class="text-muted fw-normal">(optional, max 5 files, 10MB each)</small></label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.zip,.rar">
                        <?php $__errorArgs = ['attachments.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="fileList" class="mt-2"></div>
                    </div>

                    <div class="alert alert-info py-2 mb-3" style="font-size:0.83rem">
                        <i class="bi bi-info-circle me-1"></i>
                        Emails are only sent to users who have <strong>email enabled</strong> for General Notifications in their preferences.
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="sendBtn">
                        <i class="bi bi-send me-1"></i>Send Email
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Sent Log</h5>
                <span class="badge bg-secondary"><?php echo e($logs->total()); ?> total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Recipients</th>
                            <th>Sent / Failed</th>
                            <th>Status</th>
                            <th>Sent By</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="max-width:200px">
                                <div class="text-truncate fw-semibold" style="font-size:0.88rem" title="<?php echo e($log->subject); ?>">
                                    <?php echo e($log->subject); ?>

                                    <?php if($log->attachments): ?>
                                    <i class="bi bi-paperclip text-muted ms-1" title="<?php echo e(count($log->attachments)); ?> attachment(s)"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="text-muted text-truncate" style="font-size:0.78rem;max-width:180px" title="<?php echo e($log->body); ?>">
                                    <?php echo e($log->body); ?>

                                </div>
                                <?php if($log->attachments): ?>
                                <div class="mt-1">
                                    <?php $__currentLoopData = $log->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(asset('storage/' . $att)); ?>" target="_blank" class="badge bg-light text-dark border me-1 mb-1" style="font-size:.72rem;text-decoration:none">
                                        <i class="bi bi-file-earmark me-1"></i><?php echo e(basename($att)); ?>

                                    </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.83rem"><?php echo e($log->recipient_label); ?></td>
                            <td style="font-size:0.85rem">
                                <span class="text-success fw-semibold"><?php echo e($log->sent_count); ?></span>
                                <?php if($log->failed_count): ?>
                                / <span class="text-danger"><?php echo e($log->failed_count); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($log->status === 'sent'): ?>
                                    <span class="badge bg-success">Sent</span>
                                <?php elseif($log->status === 'partial'): ?>
                                    <span class="badge bg-warning text-dark">Partial</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.83rem"><?php echo e($log->sender?->name ?? '—'); ?></td>
                            <td style="font-size:0.8rem;white-space:nowrap" class="text-muted">
                                <?php echo e($log->created_at->format('d M Y')); ?><br>
                                <?php echo e($log->created_at->format('H:i')); ?>

                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end align-items-center">
                                    <?php if($log->failed_count > 0): ?>
                                    <form method="POST" action="<?php echo e(route('admin.email-notifications.resend', $log)); ?>" class="d-inline"
                                          onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Resending…'; return true;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Resend to <?php echo e($log->failed_count); ?> failed recipient(s)">
                                            <i class="bi bi-arrow-repeat me-1"></i>Resend <span class="badge bg-danger ms-1"><?php echo e($log->failed_count); ?></span>
                                        </button>
                                    </form>
                                    <?php elseif($log->status === 'sent'): ?>
                                    <span class="text-success small"><i class="bi bi-check-circle"></i></span>
                                    <?php else: ?>
                                    <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('admin.email-notifications.destroy', $log)); ?>" class="d-inline"
                                          onsubmit="return confirm('Delete this email log? This cannot be undone.')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete log">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2 opacity-25"></i>
                                No emails have been sent yet.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($logs->hasPages()): ?>
            <div class="card-footer d-flex align-items-center justify-content-between py-2">
                <div class="text-muted" style="font-size:0.85rem">
                    Showing <?php echo e($logs->firstItem()); ?>–<?php echo e($logs->lastItem()); ?> of <?php echo e($logs->total()); ?>

                </div>
                <?php echo e($logs->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleRecipientOptions(val) {
    ['deptOption','roleOption','individualOption'].forEach(id => {
        document.getElementById(id).classList.add('d-none');
    });
    if (val === 'department') document.getElementById('deptOption').classList.remove('d-none');
    if (val === 'role')       document.getElementById('roleOption').classList.remove('d-none');
    if (val === 'individual') document.getElementById('individualOption').classList.remove('d-none');
}

document.getElementById('composeForm').addEventListener('submit', function() {
    document.getElementById('sendBtn').disabled = true;
    document.getElementById('sendBtn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';
});

// Attachment file list preview
document.getElementById('attachments').addEventListener('change', function() {
    const list = document.getElementById('fileList');
    list.innerHTML = '';
    if (this.files.length > 5) {
        list.innerHTML = '<div class="text-danger small"><i class="bi bi-exclamation-circle me-1"></i>Maximum 5 files allowed.</div>';
        this.value = '';
        return;
    }
    const icons = { pdf:'bi-file-earmark-pdf text-danger', doc:'bi-file-earmark-word text-primary', docx:'bi-file-earmark-word text-primary', xls:'bi-file-earmark-excel text-success', xlsx:'bi-file-earmark-excel text-success', ppt:'bi-file-earmark-ppt text-warning', pptx:'bi-file-earmark-ppt text-warning', jpg:'bi-file-earmark-image text-info', jpeg:'bi-file-earmark-image text-info', png:'bi-file-earmark-image text-info', gif:'bi-file-earmark-image text-info', webp:'bi-file-earmark-image text-info', zip:'bi-file-earmark-zip text-secondary', rar:'bi-file-earmark-zip text-secondary' };
    Array.from(this.files).forEach(f => {
        const ext = f.name.split('.').pop().toLowerCase();
        const icon = icons[ext] || 'bi-file-earmark text-muted';
        const size = f.size < 1024*1024 ? (f.size/1024).toFixed(1)+' KB' : (f.size/1024/1024).toFixed(1)+' MB';
        list.innerHTML += `<span class="badge bg-light text-dark border me-1 mb-1" style="font-size:.78rem"><i class="bi ${icon} me-1"></i>${f.name} <small class="text-muted">(${size})</small></span>`;
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/admin/email-notifications/index.blade.php ENDPATH**/ ?>