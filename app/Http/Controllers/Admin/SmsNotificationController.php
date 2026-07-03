<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\SmsNotificationLog;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SmsNotificationController extends Controller
{
    public function index()
    {
        $logs        = SmsNotificationLog::with('sender')->latest()->paginate(20);
        $departments = Department::orderBy('name')->get();
        $roles       = Role::orderBy('name')->get();
        $driver      = config('sms.driver', env('SMS_DRIVER', 'log'));
        $configured  = $driver !== 'log' && env('SMS_API_KEY') && env('SMS_USERNAME');

        return view('admin.sms-notifications.index', compact('logs', 'departments', 'roles', 'driver', 'configured'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message'        => 'required|string|max:160',
            'recipient_type' => 'required|in:all,students,staff,department,role,individual',
            'department_id'  => 'nullable|required_if:recipient_type,department|exists:departments,id',
            'role'           => 'nullable|required_if:recipient_type,role',
            'user_ids'       => 'nullable|required_if:recipient_type,individual|string',
        ]);

        $recipientType   = $request->recipient_type;
        $recipientFilter = null;
        $users           = collect();

        switch ($recipientType) {
            case 'all':
                $users = User::where('is_active', true)->whereNotNull('phone')->get();
                break;

            case 'students':
                $users = User::role('student')->where('is_active', true)->whereNotNull('phone')->get();
                break;

            case 'staff':
                $users = User::whereHas('roles', fn($q) => $q->whereIn('name', [
                    'super-admin', 'registrar', 'lecturer', 'librarian', 'accountant', 'hr',
                ]))->where('is_active', true)->whereNotNull('phone')->get();
                break;

            case 'department':
                $recipientFilter = ['department_id' => $request->department_id];
                $users = User::whereHas('student', fn($q) => $q->where('department_id', $request->department_id))
                    ->orWhereHas('staff', fn($q) => $q->where('department_id', $request->department_id))
                    ->where('is_active', true)->whereNotNull('phone')->get();
                break;

            case 'role':
                $recipientFilter = ['role' => $request->role];
                $users = User::role($request->role)->where('is_active', true)->whereNotNull('phone')->get();
                break;

            case 'individual':
                $ids             = array_filter(array_map('trim', explode(',', $request->user_ids)));
                $recipientFilter = ['user_ids' => $ids];
                $users = User::whereIn('id', $ids)->where('is_active', true)->whereNotNull('phone')->get();
                break;
        }

        ['sent' => $sent, 'failed' => $failed] = SmsService::sendBulk($users, 'general', $request->message);

        $status = match(true) {
            $failed === 0 => 'sent',
            $sent  === 0  => 'failed',
            default       => 'partial',
        };

        SmsNotificationLog::create([
            'message'          => $request->message,
            'recipient_type'   => $recipientType,
            'recipient_filter' => $recipientFilter,
            'sent_count'       => $sent,
            'failed_count'     => $failed,
            'status'           => $status,
            'sent_by'          => auth()->id(),
        ]);

        $msg = "SMS sent to {$sent} recipient(s)";
        if ($failed) $msg .= " ({$failed} failed)";

        return back()->with('success', $msg);
    }
}
