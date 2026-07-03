<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount('semesters')->orderBy('start_date', 'desc')->get();
        return view('academic.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('academic.academic-years.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        AcademicYear::create($request->except('_token'));
        return redirect()->route('academic.academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['semesters']);
        return view('academic.academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academic.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $academicYear->update($request->except('_token', '_method'));
        return redirect()->route('academic.academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('academic.academic-years.index')
            ->with('success', 'Academic year deleted.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });
        return back()->with('success', "{$academicYear->name} is now the current academic year.");
    }
}
