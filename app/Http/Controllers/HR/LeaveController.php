<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee.user', 'leaveType']);
        if ($request->status) $query->where('status', $request->status);
        $leaves     = $query->latest()->paginate(20);
        $leaveTypes = LeaveType::all();
        $employees  = Employee::with('user')->active()->get();

        $stats = [
            'total'    => LeaveRequest::count(),
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'this_month' => LeaveRequest::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count(),
            'by_type'  => LeaveRequest::selectRaw('leave_type_id, count(*) as total')
                                ->groupBy('leave_type_id')->with('leaveType')->get(),
        ];

        return view('hr.leave.index', compact('leaves', 'leaveTypes', 'employees', 'stats'));
    }

    public function apply(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'reason'        => 'required|string',
        ]);
        LeaveRequest::create([
            'employee_id'   => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'reason'        => $request->reason,
            'status'        => 'pending',
        ]);
        return back()->with('success', 'Leave application submitted.');
    }

    public function approve(LeaveRequest $leave)
    {
        $leave->update(['status' => 'approved', 'remarks' => 'Approved']);

        if ($userId = optional(optional($leave->employee)->user)->id) {
            $from = \Carbon\Carbon::parse($leave->start_date)->format('d M Y');
            $to   = \Carbon\Carbon::parse($leave->end_date)->format('d M Y');
            Notification::send(
                $userId, 'leave',
                'Leave Request Approved',
                "Your leave request from {$from} to {$to} has been approved.",
                [], route('hr.leave.index')
            );
        }

        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $reason = $request->reason ?: 'Rejected';
        $leave->update(['status' => 'rejected', 'remarks' => $reason]);

        if ($userId = optional(optional($leave->employee)->user)->id) {
            $from = \Carbon\Carbon::parse($leave->start_date)->format('d M Y');
            $to   = \Carbon\Carbon::parse($leave->end_date)->format('d M Y');
            Notification::send(
                $userId, 'leave',
                'Leave Request Rejected',
                "Your leave request from {$from} to {$to} was rejected. Reason: {$reason}",
                [], route('hr.leave.index')
            );
        }

        return back()->with('success', 'Leave rejected.');
    }
}
