<?php $__env->startSection('title', 'Sign In'); ?>
<?php $__env->startSection('content'); ?>
<?php if(!empty($__uni['logo_path'])): ?>
<div class="mb-3">
    <img src="<?php echo e(asset('storage/' . $__uni['logo_path'])); ?>" alt="Logo"
         style="height:56px;max-width:60px;object-fit:contain;border-radius:12px">
</div>
<?php else: ?>
<div class="auth-logo"><?php echo e(strtoupper(substr($__uni['university_short_name'] ?? 'U', 0, 2))); ?></div>
<?php endif; ?>
<h2 class="fw-700 mb-1" style="font-size:1.6rem; font-weight:700"><?php echo e($__uni['university_name'] ?? 'Welcome back'); ?></h2>
<p class="text-muted mb-4">Sign in to your account to continue</p>

<?php if(session('status')): ?>
<div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
<div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>
    <div class="mb-3">
        <label class="form-label fw-semibold">Email Address</label>
        <input type="email" name="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="admin@university.com" value="<?php echo e(old('email')); ?>" required autofocus>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold mb-0">Password</label>
            <a href="<?php echo e(route('password.request')); ?>" class="text-muted" style="font-size:0.85rem">Forgot password?</a>
        </div>
        <div class="input-group">
            <input type="password" name="password" class="form-control border-end-0 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required id="password-field">
            <span class="input-group-text" onclick="togglePassword()">
                <i class="bi bi-eye" id="pass-eye"></i>
            </span>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted" for="remember">Remember me</label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary btn-login text-white">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
    </button>
</form>

<div class="mt-4 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e9ecef">
    <div class="fw-semibold mb-2" style="font-size:0.82rem; color:#6c757d; text-transform:uppercase; letter-spacing:0.05em">Demo Credentials</div>
    <div style="font-size:0.85rem">
        <div><strong>Email:</strong> admin@university.com</div>
        <div><strong>Password:</strong> Admin@123</div>
    </div>
</div>

<script>
function togglePassword() {
    const f = document.getElementById('password-field');
    const e = document.getElementById('pass-eye');
    f.type = f.type === 'password' ? 'text' : 'password';
    e.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/auth/login.blade.php ENDPATH**/ ?>