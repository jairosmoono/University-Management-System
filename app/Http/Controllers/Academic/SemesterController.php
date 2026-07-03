<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('academicYear')->withCount('courseOfferings')->orderBy('start_date', 'desc')->get();
        return view('academic.semesters.index', compact('semesters'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('academic.semesters.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        Semester::create($request->except('_token'));
        return redirect()->route('academic.semesters.index')
            ->with('success', 'Semester/Term created successfully.');
    }

    public function show(Semester $semester)
    {
        $semester->load(['academicYear', 'courseOfferings.course', 'courseOfferings.lecturer']);
        return view('academic.semesters.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('academic.semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $semester->update($request->except('_token', '_method'));
        return redirect()->route('academic.semesters.index')
            ->with('success', 'Semester/Term updated successfully.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('academic.semesters.index')->with('success', 'Semester/Term deleted.');
    }

    public function setCurrent(Semester $semester)
    {
        DB::transaction(function () use ($semester) {
            Semester::where('is_current', true)->update(['is_current' => false]);
            $semester->update(['is_current' => true, 'status' => 'active']);
        });
        return back()->with('success', "{$semester->name} is now the current semester/term.");
    }
}
