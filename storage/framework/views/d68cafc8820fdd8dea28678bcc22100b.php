<?php $__env->startSection('title', '404 Not Found'); ?>
<?php $__env->startSection('page-title', '404 Not Found'); ?>

<?php $__env->startSection('content'); ?>
<div class="text-center py-5">
    <div style="font-size:5rem; font-weight:800; color:#dee2e6; line-height:1">404</div>
    <h2 class="fw-bold mb-3 mt-2">Page Not Found</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">The page you're looking for doesn't exist or has been moved.</p>
    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary text-white px-4">
        <i class="bi bi-house me-2"></i>Back to Dashboard
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/errors/404.blade.php ENDPATH**/ ?>