<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Login'); ?> - <?php echo e($__uni['university_name'] ?? config('app.name', 'University Management System')); ?></title>
    <?php if(!empty($__uni['favicon_path'])): ?>
    <link rel="icon" href="<?php echo e(asset('storage/' . $__uni['favicon_path'])); ?>" type="image/x-icon">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --primary: #0B1F3A; --secondary: #8B0000; }
        body { background: linear-gradient(135deg, #0B1F3A 0%, #1a3a6b 50%, #8B0000 100%); min-height: 100vh; display: flex; align-items: center; }
        .auth-card { background: white; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.3); overflow: hidden; }
        .auth-left { background: linear-gradient(160deg, #0B1F3A, #1a3a6b); padding: 3rem; color: white; display: flex; flex-direction: column; justify-content: center; }
        .auth-left h1 { font-size: 2rem; font-weight: 800; margin-bottom: 1rem; }
        .auth-left p { opacity: 0.8; line-height: 1.7; }
        .auth-feature { display: flex; align-items: center; gap: 12px; margin-bottom: 1rem; }
        .auth-feature-icon { width: 36px; height: 36px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .auth-right { padding: 3rem; }
        .auth-logo { width: 56px; height: 56px; background: var(--secondary); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.4rem; color: white; margin-bottom: 1.5rem; }
        .form-control { border-radius: 10px; padding: 0.75rem 1rem; border-color: #e0e0e0; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(11,31,58,0.1); }
        .btn-login { background: var(--primary); border: none; border-radius: 10px; padding: 0.75rem; font-weight: 600; font-size: 1rem; width: 100%; }
        .btn-login:hover { background: #1a3a6b; }
        .input-group-text { background: #f8f9fa; border-color: #e0e0e0; border-radius: 0 10px 10px 0; cursor: pointer; }
        @media (max-width: 767.98px) { .auth-left { display: none !important; } }
    </style>
</head>
<?php
    $__settingsPath = storage_path('app/settings.json');
    $__uni = file_exists($__settingsPath) ? (json_decode(file_get_contents($__settingsPath), true) ?? []) : [];
?>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="auth-card">
                <div class="row g-0">
                    <div class="col-md-5 auth-left">
                        <div>
                            <?php if(!empty($__uni['logo_path'])): ?>
                            <img src="<?php echo e(asset('storage/' . $__uni['logo_path'])); ?>" alt="Logo"
                                 style="height:60px;max-width:70px;object-fit:contain;border-radius:12px;background:#fff;padding:4px;margin-bottom:0.75rem">
                            <?php else: ?>
                            <div style="font-size:2.5rem; margin-bottom:0.5rem">&#127891;</div>
                            <?php endif; ?>
                            <h1><?php echo e($__uni['university_name'] ?? config('app.name', 'University Management System')); ?></h1>
                            <p>A comprehensive platform for managing all aspects of university operations.</p>
                            <div class="mt-4">
                                <?php $__currentLoopData = [['bi-mortarboard', 'Academic Management'], ['bi-people', 'Student Services'], ['bi-cash-stack', 'Finance & Billing'], ['bi-bar-chart', 'Analytics & Reports']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="auth-feature">
                                    <div class="auth-feature-icon"><i class="bi <?php echo e($icon); ?>"></i></div>
                                    <span><?php echo e($label); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 auth-right">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/layouts/auth.blade.php ENDPATH**/ ?>