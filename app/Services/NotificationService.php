<?php
namespace App\Services;

use App\Mail\SystemNotificationMail;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotificationService
{
    public static function send(
        int     $userId,
        string  $type,
        string  $title,
        string  $message,
        array   $data      = [],
        ?string $actionUrl = null,
        string  $actionLabel = 'View Details',
    ): Notification {
        $notif = Notification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'data'       => $data,
            'action_url' => $actionUrl,
            'is_read'    => false,
        ]);

        $user = User::find($userId);

        if ($user && static::userWantsEmail($user, $type)) {
            static::sendEmail($user, $type, $title, $message, $actionUrl, $actionLabel);
        }

        if ($user) {
            SmsService::sendForType($user, $type, static::smsText($title, $message));
        }

        return $notif;
    }

    public static function sendEmail(
        User    $user,
        string  $type,
        string  $title,
        string  $message,
        ?string $actionUrl       = null,
        string  $actionLabel     = 'View Details',
        array   $attachmentPaths = [],
    ): bool {
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) return false;

        try {
            Mail::to($user->email)->send(new SystemNotificationMail(
                notifTitle:      $title,
                notifMessage:    $message,
                recipientName:   $user->name,
                actionUrl:       $actionUrl,
                actionLabel:     $actionLabel,
                notifType:       $type,
                attachmentPaths: $attachmentPaths,
            ));
            return true;
        } catch (\Throwable $e) {
            Log::error("Email notification failed for user {$user->id} ({$user->email}): " . $e->getMessage());
            return false;
        }
    }

    public static function userWantsEmail(User $user, string $type): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('type', $type)
            ->first();

        return $pref ? $pref->email_enabled : true;
    }

    public static function sendBulkEmail(
        iterable $users,
        string   $type,
        string   $title,
        string   $message,
        ?string  $actionUrl       = null,
        string   $actionLabel     = 'View Details',
        array    $attachmentPaths = [],
    ): array {
        $sent = $failed = 0;

        foreach ($users as $user) {
            if (!($user instanceof User)) {
                $user = User::find($user);
                if (!$user) { $failed++; continue; }
            }
            if (static::sendEmail($user, $type, $title, $message, $actionUrl, $actionLabel, $attachmentPaths)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    public static function smsText(string $title, string $message): string
    {
        $appName = config('app.name', 'University');
        $body    = Str::limit(strip_tags($message), 100);
        return "[{$appName}] {$title}: {$body}";
    }
}
