<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmailNotificationLog;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class EmailNotificationController extends Controller
{
    public function index()
    {
        $logs       = EmailNotificationLog::with('sender')->latest()->paginate(20);
        $departments = Department::orderBy('name')->get();
        $roles      = Role::orderBy('name')->get();

        return view('admin.email-notifications.index', compact('logs', 'departments', 'roles'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject'        => 'required|string|max:255',
            'body'           => 'required|string',
            'recipient_type' => 'required|in:all,students,staff,department,role,individual',
            'department_id'  => 'nullable|required_if:recipient_type,department|exists:departments,id',
            'role'           => 'nullable|required_if:recipient_type,role',
            'user_ids'       => 'nullable|required_if:recipient_type,individual|string',
            'attachments'    => 'nullable|array|max:5',
            'attachments.*'  => 'file|max:10240',
        ]);

        $recipientType   = $request->recipient_type;
        $recipientFilter = null;
        $users           = collect();

        switch ($recipientType) {
            case 'all':
                $users = User::where('is_active', true)->get();
                break;

            case 'students':
                $users = User::role('student')->where('is_active', true)->get();
                break;

            case 'staff':
                $users = User::whereHas('roles', function ($q) {
                    $q->whereIn('name', ['super-admin', 'registrar', 'lecturer', 'librarian', 'accountant', 'hr']);
                })->where('is_active', true)->get();
                break;

            case 'department':
                $recipientFilter = ['department_id' => $request->department_id];
                $users = User::whereHas('student', fn($q) => $q->where('department_id', $request->department_id))
                    ->orWhereHas('staff', fn($q) => $q->where('department_id', $request->department_id))
                    ->where('is_active', true)
                    ->get();
                break;

            case 'role':
                $recipientFilter = ['role' => $request->role];
                $users = User::role($request->role)->where('is_active', true)->get();
                break;

            case 'individual':
                $ids   = array_filter(array_map('trim', explode(',', $request->user_ids)));
                $recipientFilter = ['user_ids' => $ids];
                $users = User::whereIn('id', $ids)->where('is_active', true)->get();
                break;
        }

        set_time_limit(300);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('email-attachments', 'public');
            }
        }

        $result = $this->sendToUsers($users, $request->subject, $request->body, $attachmentPaths);

        EmailNotificationLog::create([
            'subject'          => $request->subject,
            'body'             => $request->body,
            'attachments'      => $attachmentPaths ?: null,
            'recipient_type'   => $recipientType,
            'recipient_filter' => $recipientFilter,
            'sent_count'       => $result['sent'],
            'failed_count'     => $result['failed'],
            'sent_user_ids'    => $result['sent_ids'] ?: null,
            'failed_user_ids'  => $result['failed_ids'] ?: null,
            'status'           => $result['status'],
            'sent_by'          => auth()->id(),
        ]);

        if ($result['timed_out']) {
            $msg = "The operation timed out after sending to {$result['sent']} of {$result['total']} recipient(s). "
                 . "The unsent emails can be resent using the Resend button.";
            return back()->with('warning', $msg);
        }

        $msg = "Email sent to {$result['sent']} recipient(s)";
        if ($result['failed']) $msg .= " ({$result['failed']} failed)";

        return back()->with('success', $msg);
    }

    public function destroy(EmailNotificationLog $log)
    {
        if ($log->attachments) {
            foreach ($log->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        $log->delete();
        return back()->with('success', 'Email log deleted successfully.');
    }

    public function resend(EmailNotificationLog $log)
    {
        $failedIds = $log->failed_user_ids ?? [];

        if (empty($failedIds)) {
            $users = $this->resolveRecipients($log->recipient_type, $log->recipient_filter);
            $sentIds = $log->sent_user_ids ?? [];
            if (!empty($sentIds)) {
                $users = $users->whereNotIn('id', $sentIds);
            }
        } else {
            $users = User::whereIn('id', $failedIds)->where('is_active', true)->get();
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'No recipients to resend to.');
        }

        if ($users->isEmpty()) {
            return back()->with('error', 'No active users found among the failed recipients.');
        }

        set_time_limit(300);

        $attachmentPaths = $log->attachments ?? [];

        $result = $this->sendToUsers($users, $log->subject, $log->body, $attachmentPaths);

        $previousSentIds = $log->sent_user_ids ?? [];
        $allSentIds      = array_values(array_unique(array_merge($previousSentIds, $result['sent_ids'])));
        $remainingFailed = $result['failed_ids'];

        $log->update([
            'sent_count'      => count($allSentIds),
            'failed_count'    => count($remainingFailed),
            'sent_user_ids'   => $allSentIds ?: null,
            'failed_user_ids' => $remainingFailed ?: null,
            'status'          => empty($remainingFailed) ? 'sent' : (empty($allSentIds) ? 'failed' : 'partial'),
        ]);

        if ($result['timed_out']) {
            $msg = "Resend timed out. {$result['sent']} more email(s) delivered. "
                 . count($remainingFailed) . " still pending — you can resend again.";
            return back()->with('warning', $msg);
        }

        if ($result['failed']) {
            return back()->with('warning', "Resent to {$result['sent']} recipient(s), but {$result['failed']} still failed.");
        }

        return back()->with('success', "Successfully resent to {$result['sent']} recipient(s). All emails delivered.");
    }

    private function sendToUsers($users, string $subject, string $body, array $attachmentPaths): array
    {
        $sent = $failed = 0;
        $sentIds = $failedIds = [];
        $timedOut = false;
        $totalUsers = $users->count();
        $startTime = time();
        $maxSeconds = (int) ini_get('max_execution_time') ?: 300;
        $safeLimit = max($maxSeconds - 10, 15);

        foreach ($users as $user) {
            if ((time() - $startTime) >= $safeLimit) {
                $timedOut = true;
                break;
            }

            try {
                if (NotificationService::sendEmail(
                    $user, 'general', $subject, $body, null, 'View', $attachmentPaths
                )) {
                    $sent++;
                    $sentIds[] = $user->id;
                } else {
                    $failed++;
                    $failedIds[] = $user->id;
                }
            } catch (\Throwable $e) {
                $failed++;
                $failedIds[] = $user->id;
                Log::error("Email send error for user {$user->id}: " . $e->getMessage());
            }
        }

        if ($timedOut) {
            $remaining = $users->pluck('id')->diff(array_merge($sentIds, $failedIds))->values()->all();
            $failedIds = array_merge($failedIds, $remaining);
            $failed += count($remaining);
        }

        $status = match(true) {
            $sent === 0   => 'failed',
            $failed === 0 => 'sent',
            default       => 'partial',
        };

        return [
            'sent'       => $sent,
            'failed'     => $failed,
            'total'      => $totalUsers,
            'sent_ids'   => $sentIds,
            'failed_ids' => $failedIds,
            'timed_out'  => $timedOut,
            'status'     => $status,
        ];
    }

    private function resolveRecipients(string $type, ?array $filter): \Illuminate\Support\Collection
    {
        return match ($type) {
            'all'        => User::where('is_active', true)->get(),
            'students'   => User::role('student')->where('is_active', true)->get(),
            'staff'      => User::whereHas('roles', fn($q) => $q->whereIn('name', ['super-admin','registrar','lecturer','librarian','accountant','hr']))->where('is_active', true)->get(),
            'department' => User::where(fn($q) => $q->whereHas('student', fn($s) => $s->where('department_id', $filter['department_id'] ?? 0))->orWhereHas('staff', fn($s) => $s->where('department_id', $filter['department_id'] ?? 0)))->where('is_active', true)->get(),
            'role'       => User::role($filter['role'] ?? '')->where('is_active', true)->get(),
            'individual' => User::whereIn('id', $filter['user_ids'] ?? [])->where('is_active', true)->get(),
            default      => collect(),
        };
    }
}
