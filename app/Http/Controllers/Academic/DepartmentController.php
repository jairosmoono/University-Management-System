<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Staff;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['faculty', 'hod.user'])->withCount(['programs', 'courses', 'students']);
        if ($request->faculty_id) $query->where('faculty_id', $request->faculty_id);
        $departments = $query->get();
        $faculties   = Faculty::active()->get();
        $staff       = Staff::with('user')->active()->orderBy('id')->get();
        return view('academic.departments.index', compact('departments', 'faculties', 'staff'));
    }

    public function create()
    {
        $faculties = Faculty::active()->get();
        $staff = Staff::with('user')->active()->orderBy('id')->get();
        return view('academic.departments.create', compact('faculties', 'staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code',
        ]);

        Department::create($request->except('_token'));
        return redirect()->route('academic.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['faculty', 'hod.user', 'programs', 'courses', 'staff.user', 'students.user']);
        return view('academic.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::active()->get();
        $staff = Staff::with('user')->active()->orderBy('id')->get();
        $department->load('hod.user');
        return view('academic.departments.edit', compact('department', 'faculties', 'staff'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
        ]);

        $department->update($request->except('_token', '_method'));
        return redirect()->route('academic.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('academic.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function byFaculty(Faculty $faculty)
    {
        return response()->json(Department::where('faculty_id', $faculty->id)->active()->get(['id', 'name', 'code']));
    }
}
