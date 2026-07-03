<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with(['department.faculty'])->withCount('students');
        if ($request->faculty_id) {
            $query->whereHas('department', fn($q) => $q->where('faculty_id', $request->faculty_id));
        }
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        $programs = $query->orderBy('name')->paginate(20);
        $faculties = Faculty::active()->get();
        $departments = Department::active()->orderBy('name')->get();
        return view('academic.programs.index', compact('programs', 'faculties', 'departments'));
    }

    public function create()
    {
        $departments = Department::with('faculty')->active()->get();
        $faculties = Faculty::active()->get();
        return view('academic.programs.create', compact('departments', 'faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id'  => 'required|exists:departments,id',
            'name'           => 'required|string|max:255',
            'code'           => 'required|string|max:20|unique:programs,code',
            'level'          => 'required|in:degree,diploma,certificate,craft_certificate,trade_test_certificate',
            'duration_years' => 'required|integer|min:1',
            'duration_unit'  => 'required|in:years,months',
        ]);

        Program::create($request->only(['department_id', 'name', 'code', 'level', 'duration_years', 'duration_unit', 'credit_hours_required', 'description', 'status']));
        return redirect()->route('academic.programs.index')->with('success', 'Program created successfully.');
    }

    public function show(Program $program)
    {
        $program->load(['department.faculty', 'students.user', 'courses']);
        return view('academic.programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        $departments = Department::with('faculty')->active()->get();
        $faculties = Faculty::active()->get();
        return view('academic.programs.edit', compact('program', 'departments', 'faculties'));
    }

    public function update(Request $request, Program $program)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:20|unique:programs,code,' . $program->id,
        ]);

        $program->update($request->only(['department_id', 'name', 'code', 'level', 'duration_years', 'duration_unit', 'credit_hours_required', 'description', 'status']));
        return redirect()->route('academic.programs.index')->with('success', 'Program updated successfully.');
    }

    public function destroy(Program $program)
    {
        if ($program->students()->count() > 0) {
            return back()->with('error', 'Cannot delete program with enrolled students.');
        }
        $program->delete();
        return redirect()->route('academic.programs.index')->with('success', 'Program deleted successfully.');
    }

    public function byDepartment(Department $department)
    {
        return response()->json(Program::where('department_id', $department->id)->active()->get(['id', 'name', 'code', 'level']));
    }

    public function byFaculty(Request $request)
    {
        $facultyId = $request->faculty_id;
        $programs  = Program::whereHas('department', fn($q) => $q->where('faculty_id', $facultyId))
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'level']);

        return response()->json($programs);
    }
}
