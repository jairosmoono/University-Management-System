<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::with('dean.user')
            ->withCount('departments')
            ->get();
        $staff = Staff::active()->with('user')->get();
        return view('academic.faculties.index', compact('faculties', 'staff'));
    }

    public function create()
    {
        $staff = Staff::active()->get();
        return view('academic.faculties.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
        ]);

        Faculty::create($request->except('_token'));
        return redirect()->route('academic.faculties.index')
            ->with('success', 'Faculty created successfully.');
    }

    public function show(Faculty $faculty)
    {
        $faculty->load(['departments', 'dean.user']);
        $faculty->load(['programs' => fn($q) => $q->with('department')]);
        $students = Student::with('user')
            ->whereIn('program_id', $faculty->programs->pluck('id'))
            ->get();
        $faculty->setRelation('students', $students);
        return view('academic.faculties.show', compact('faculty'));
    }

    public function edit(Faculty $faculty)
    {
        $staff = Staff::active()->get();
        return view('academic.faculties.edit', compact('faculty', 'staff'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code,' . $faculty->id,
        ]);

        $faculty->update($request->except('_token', '_method'));
        return redirect()->route('academic.faculties.index')
            ->with('success', 'Faculty updated successfully.');
    }

    public function destroy(Faculty $faculty)
    {
        $programIds = Program::whereIn('department_id', $faculty->departments()->pluck('id'))->pluck('id');
        if (Student::whereIn('program_id', $programIds)->count() > 0) {
            return back()->with('error', 'Cannot delete faculty with active students.');
        }
        $faculty->delete();
        return redirect()->route('academic.faculties.index')
            ->with('success', 'Faculty deleted successfully.');
    }
}
