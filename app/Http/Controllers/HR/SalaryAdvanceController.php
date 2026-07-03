<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryAdvance::with('employee.user');
        if ($request->status)      $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);

        $advances  = $query->latest()->paginate(20);
        $employees = Employee::with('user')->active()->get();

        $stats = [
            'total'    => SalaryAdvance::count(),
            'pending'  => SalaryAdvance::where('status', 'pending')->count(),
            'approved' => SalaryAdvance::where('status', 'approved')->count(),
            'rejected' => SalaryAdvance::where('status', 'rejected')->count(),
            'paid'     => SalaryAdvance::where('status', 'paid')->count(),
            'total_approved_amount' => SalaryAdvance::whereIn('status', ['approved', 'paid'])
                                            ->sum('amount_approved'),
        ];

        return view('hr.salary-advances.index', compact('advances', 'employees', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'amount_requested' => 'required|numeric|min:1',
            'reason'           => 'required|string',
            'request_date'     => 'required|date',
            'repayment_months' => 'required|integer|min:1|max:24',
        ]);

        SalaryAdvance::create([
            'employee_id'      => $request->employee_id,
            'amount_requested' => $request->amount_requested,
            'reason'           => $request->reason,
            'request_date'     => $request->request_date,
            'repayment_months' => $request->repayment_months,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Salary advance request submitted.');
    }

    public function approve(Request $request, SalaryAdvance $salaryAdvance)
    {
        $request->validate([
            'amount_approved'       => 'required|numeric|min:1',
            'repayment_start_date'  => 'required|date',
            'repayment_months'      => 'required|integer|min:1|max:24',
        ]);

        $salaryAdvance->update([
            'status'               => 'approved',
            'amount_approved'      => $request->amount_approved,
            'repayment_start_date' => $request->repayment_start_date,
            'repayment_months'     => $request->repayment_months,
            'remarks'              => $request->remarks,
        ]);

        $salaryAdvance->load('employee.user');
        if ($userId = optional(optional($salaryAdvance->employee)->user)->id) {
            Notification::send(
                $userId, 'payment',
                'Salary Advance Approved',
                'Your salary advance of ' . formatCurrency($request->amount_approved) . ' has been approved. Repayment starts ' . \Carbon\Carbon::parse($request->repayment_start_date)->format('M Y') . '.',
                [], route('hr.salary-advances.index')
            );
        }

        return back()->with('success', 'Salary advance approved.');
    }

    public function reject(Request $request, SalaryAdvance $salaryAdvance)
    {
        $request->validate(['remarks' => 'required|string|min:5']);
        $salaryAdvance->update(['status' => 'rejected', 'remarks' => $request->remarks]);

        $salaryAdvance->load('employee.user');
        if ($userId = optional(optional($salaryAdvance->employee)->user)->id) {
            Notification::send(
                $userId, 'payment',
                'Salary Advance Rejected',
                'Your salary advance request of ' . formatCurrency($salaryAdvance->amount_requested) . ' was rejected. Reason: ' . $request->remarks,
                [], route('hr.salary-advances.index')
            );
        }

        return back()->with('success', 'Salary advance rejected.');
    }

    public function markPaid(SalaryAdvance $salaryAdvance)
    {
        $salaryAdvance->update(['status' => 'paid']);
        return back()->with('success', 'Salary advance marked as paid.');
    }

    public function destroy(SalaryAdvance $salaryAdvance)
    {
        if (!in_array($salaryAdvance->status, ['pending', 'rejected'])) {
            return back()->with('error', 'Only pending or rejected advances can be deleted.');
        }
        $salaryAdvance->delete();
        return back()->with('success', 'Salary advance deleted.');
    }
}
