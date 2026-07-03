<?php $__env->startSection('title', '419 Session Expired'); ?>
<?php $__env->startSection('page-title', 'Session Expired'); ?>

<?php $__env->startSection('content'); ?>
<div class="text-center py-5">
    <div style="font-size:5rem; line-height:1">&#9200;</div>
    <h2 class="fw-bold mb-3 mt-2">Session Expired</h2>
    <p class="text-muted mb-4 mx-auto" style="max-width:400px">Your session has expired due to inactivity. Please go back and try your action again.</p>
    <a href="javascript:history.back()" class="btn btn-outline-secondary px-4 me-2">
        <i class="bi bi-arrow-left me-2"></i>Go Back
    </a>
    <a href="<?php echo e(route('login')); ?>" class="btn btn-primary text-white px-4">
        <i class="bi bi-box-arrow-in-right me-2"></i>Log In Again
    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/errors/419.blade.php ENDPATH**/ ?>