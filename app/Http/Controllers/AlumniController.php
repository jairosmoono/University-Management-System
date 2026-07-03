<?php
namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $query = Alumni::with(['student.user', 'student.program']);
        if ($request->graduation_year)   $query->where('graduation_year', $request->graduation_year);
        if ($request->employment_status) $query->where('employment_status', $request->employment_status);
        if ($request->search) {
            $s = $request->search;
            $query->whereHas('student.user', fn($q) => $q->where('name', 'like', "%$s%"));
        }
        if ($request->program_id) {
            $query->whereHas('student', fn($q) => $q->where('program_id', $request->program_id));
        }
        $alumni   = $query->latest()->paginate(20);
        $years    = Alumni::selectRaw('DISTINCT graduation_year')->orderBy('graduation_year', 'desc')->pluck('graduation_year');
        $programs = Program::orderBy('name')->get();
        $stats  = [
            'total'          => Alumni::count(),
            'employed'       => Alumni::where('employment_status', 'employed')->count(),
            'in_business'    => Alumni::where('employment_status', 'self_employed')->count(),
            'further_studies'=> Alumni::where('employment_status', 'further_studies')->count(),
        ];
        $students = Student::with(['user', 'program'])
            ->whereHas('user')
            ->whereNotIn('id', Alumni::pluck('student_id'))
            ->get();
        return view('alumni.index', compact('alumni', 'years', 'stats', 'programs', 'students'));
    }

    public function create()
    {
        $students = Student::with(['user', 'program'])->whereIn('status', ['completed', 'registered'])->get();
        return view('alumni.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id', 'graduation_year' => 'required|integer']);
        Alumni::updateOrCreate(['student_id' => $request->student_id], $request->except('_token'));
        return redirect()->route('alumni.index')->with('success', 'Alumni record created.');
    }

    public function show(Alumni $alumnus)
    {
        $alumnus->load(['student.user', 'student.program']);
        return view('alumni.show', compact('alumnus'));
    }

    public function edit(Alumni $alumnus)
    {
        return view('alumni.edit', compact('alumnus'));
    }

    public function update(Request $request, Alumni $alumnus)
    {
        $alumnus->update($request->except('_token', '_method'));
        return redirect()->route('alumni.index')->with('success', 'Alumni record updated.');
    }

    public function destroy(Alumni $alumnus)
    {
        $alumnus->delete();
        return redirect()->route('alumni.index')->with('success', 'Alumni record deleted.');
    }
}
