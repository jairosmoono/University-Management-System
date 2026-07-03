<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeAppointment;
use Illuminate\Http\Request;

class EmployeeAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeAppointment::with(['employee.user', 'department']);
        if ($request->status)      $query->where('status', $request->status);
        if ($request->employee_id) $query->where('employee_id', $request->employee_id);
        if ($request->department_id) $query->where('department_id', $request->department_id);

        $appointments = $query->latest('appointment_date')->paginate(20);
        $employees    = Employee::with('user')->active()->get();
        $departments  = Department::orderBy('name')->get();

        $stats = [
            'total'     => EmployeeAppointment::count(),
            'active'    => EmployeeAppointment::where('status', 'active')->count(),
            'expired'   => EmployeeAppointment::where('status', 'expired')->count(),
            'terminated'=> EmployeeAppointment::where('status', 'terminated')->count(),
        ];

        return view('hr.employee-appointments.index', compact('appointments', 'employees', 'departments', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'department_id'    => 'nullable|exists:departments,id',
            'position'         => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'salary'           => 'nullable|numeric|min:0',
            'contract_type'    => 'required|in:permanent,contract,probation,acting',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:active,expired,terminated',
        ]);

        EmployeeAppointment::create($request->only([
            'employee_id', 'department_id', 'position', 'appointment_date',
            'start_date', 'end_date', 'salary', 'contract_type', 'notes', 'status',
        ]));

        return back()->with('success', 'Appointment record created successfully.');
    }

    public function update(Request $request, EmployeeAppointment $employeeAppointment)
    {
        $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'department_id'    => 'nullable|exists:departments,id',
            'position'         => 'required|string|max:255',
            'appointment_date' => 'required|date',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'salary'           => 'nullable|numeric|min:0',
            'contract_type'    => 'required|in:permanent,contract,probation,acting',
            'notes'            => 'nullable|string',
            'status'           => 'required|in:active,expired,terminated',
        ]);

        $employeeAppointment->update($request->only([
            'employee_id', 'department_id', 'position', 'appointment_date',
            'start_date', 'end_date', 'salary', 'contract_type', 'notes', 'status',
        ]));

        return back()->with('success', 'Appointment record updated successfully.');
    }

    public function destroy(EmployeeAppointment $employeeAppointment)
    {
        $employeeAppointment->delete();
        return back()->with('success', 'Appointment record deleted.');
    }
}
