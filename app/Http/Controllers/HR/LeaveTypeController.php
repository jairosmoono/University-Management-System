<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::withCount('leaveRequests')->orderBy('name')->get();
        return view('hr.leave-types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100|unique:leave_types,name',
            'days_allowed' => 'required|integer|min:1|max:365',
            'is_paid'      => 'boolean',
            'description'  => 'nullable|string|max:500',
        ]);

        LeaveType::create([
            'name'         => $request->name,
            'days_allowed' => $request->days_allowed,
            'is_paid'      => $request->boolean('is_paid'),
            'description'  => $request->description,
        ]);

        return back()->with('success', 'Leave type created successfully.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name'         => 'required|string|max:100|unique:leave_types,name,' . $leaveType->id,
            'days_allowed' => 'required|integer|min:1|max:365',
            'is_paid'      => 'boolean',
            'description'  => 'nullable|string|max:500',
        ]);

        $leaveType->update([
            'name'         => $request->name,
            'days_allowed' => $request->days_allowed,
            'is_paid'      => $request->boolean('is_paid'),
            'description'  => $request->description,
        ]);

        return back()->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        if ($leaveType->leaveRequests()->exists()) {
            return back()->with('error', 'Cannot delete a leave type that has existing leave requests.');
        }
        $leaveType->delete();
        return back()->with('success', 'Leave type deleted.');
    }
}
