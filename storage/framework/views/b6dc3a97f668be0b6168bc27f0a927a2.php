<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo e($notifTitle); ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f4f6f9; color: #333; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
  .header { background: #0B1F3A; padding: 28px 36px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; font-weight: 700; letter-spacing: .03em; margin: 0; }
  .header p  { color: rgba(255,255,255,.65); font-size: 12px; margin-top: 4px; }
  .badge-wrap { padding: 20px 36px 0; }
  .type-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; background: #e8f0fe; color: #1a56db; }
  .body  { padding: 28px 36px; }
  .body h2 { font-size: 20px; color: #0B1F3A; margin-bottom: 14px; line-height: 1.3; }
  .greeting { font-size: 14px; color: #555; margin-bottom: 16px; }
  .message { font-size: 15px; color: #444; line-height: 1.7; }
  .cta-wrap { text-align: center; margin: 32px 0 8px; }
  .cta { display: inline-block; background: #0B1F3A; color: #fff; text-decoration: none; padding: 13px 32px; border-radius: 6px; font-size: 14px; font-weight: 600; letter-spacing: .02em; }
  .divider { height: 1px; background: #eee; margin: 28px 36px; }
  .footer { padding: 0 36px 28px; text-align: center; }
  .footer p { font-size: 12px; color: #999; line-height: 1.6; }
  .footer a { color: #0B1F3A; text-decoration: none; }
  @media (max-width:600px) {
    .header, .body, .footer, .badge-wrap, .divider { padding-left: 20px; padding-right: 20px; }
  }
</style>
</head>
<body>
<div class="wrapper">

  
  <div class="header">
    <h1><?php echo e($appName); ?></h1>
    <p>Official Notification</p>
  </div>

  
  <?php
    $badgeColors = [
      'announcement' => 'background:#fff3cd;color:#856404',
      'result'       => 'background:#cff4fc;color:#055160',
      'leave'        => 'background:#d1e7dd;color:#0a3622',
      'payment'      => 'background:#d1e7dd;color:#0a3622',
      'support'      => 'background:#f8d7da;color:#58151c',
      'admission'    => 'background:#cfe2ff;color:#084298',
      'general'      => 'background:#e2e3e5;color:#41464b',
    ];
    $badgeStyle = $badgeColors[$notifType] ?? $badgeColors['general'];
    $typeLabels = [
      'announcement' => 'Announcement',
      'result'       => 'Academic Results',
      'leave'        => 'Leave',
      'payment'      => 'Finance',
      'support'      => 'Support',
      'admission'    => 'Admissions',
      'general'      => 'Notification',
    ];
  ?>
  <div class="badge-wrap">
    <span class="type-badge" style="<?php echo e($badgeStyle); ?>">
      <?php echo e($typeLabels[$notifType] ?? 'Notification'); ?>

    </span>
  </div>

  
  <div class="body">
    <h2><?php echo e($notifTitle); ?></h2>
    <?php if($recipientName): ?>
    <p class="greeting">Hello <?php echo e($recipientName); ?>,</p>
    <?php endif; ?>
    <div class="message"><?php echo nl2br(e($notifMessage)); ?></div>

    <?php if($actionUrl): ?>
    <div class="cta-wrap">
      <a href="<?php echo e($actionUrl); ?>" class="cta"><?php echo e($actionLabel); ?></a>
    </div>
    <?php endif; ?>

    <?php if(!empty($attachmentPaths)): ?>
    <div style="margin-top:24px;padding:14px 18px;background:#f8f9fa;border-radius:6px;border:1px solid #e9ecef">
      <p style="font-size:13px;font-weight:600;color:#0B1F3A;margin:0 0 8px">
        <span style="margin-right:4px">&#128206;</span> Attachments (<?php echo e(count($attachmentPaths)); ?>)
      </p>
      <?php $__currentLoopData = $attachmentPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <p style="font-size:12px;color:#555;margin:4px 0;line-height:1.4">
        &#8226; <?php echo e(basename($path)); ?>

      </p>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
  </div>

  <div class="divider"></div>

  
  <div class="footer">
    <p>
      This email was sent by <strong><?php echo e($appName); ?></strong>.<br>
      If you believe this was sent in error, please disregard this message.<br>
      To manage your email preferences, <a href="<?php echo e(url('/notifications/preferences')); ?>">click here</a>.
    </p>
    <p style="margin-top:12px;color:#ccc">
      &copy; <?php echo e(date('Y')); ?> <?php echo e($appName); ?>. All rights reserved.
    </p>
  </div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\college_management_system\resources\views/emails/notification.blade.php ENDPATH**/ ?>