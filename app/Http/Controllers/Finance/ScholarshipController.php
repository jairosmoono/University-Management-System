<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipAward;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::withCount('awards')->get();
        $awards = ScholarshipAward::with(['scholarship', 'student.user'])->latest()->get();
        $students = Student::with('user')->active()->orderBy('student_id')->get();
        return view('finance.scholarships.index', compact('scholarships', 'awards', 'students'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('finance.scholarships.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:merit,need,sports,government,other',
            'coverage_type'  => 'required|in:percentage,fixed',
            'coverage_value' => 'required|numeric|min:0',
            'max_recipients' => 'nullable|integer|min:1',
            'description'    => 'nullable|string',
        ]);
        Scholarship::create($request->only('name', 'type', 'coverage_type', 'coverage_value', 'max_recipients', 'description') + ['status' => 'active']);
        return redirect()->route('finance.scholarships.index')->with('success', 'Scholarship created successfully.');
    }

    public function show(Scholarship $scholarship)
    {
        $scholarship->load(['awards.student', 'awards.academicYear']);
        return view('finance.scholarships.show', compact('scholarship'));
    }

    public function edit(Scholarship $scholarship)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('finance.scholarships.edit', compact('scholarship', 'academicYears'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:merit,need,sports,government,other',
            'coverage_type'  => 'required|in:percentage,fixed',
            'coverage_value' => 'required|numeric|min:0',
            'max_recipients' => 'nullable|integer|min:1',
            'status'         => 'required|in:active,inactive',
            'description'    => 'nullable|string',
        ]);

        $scholarship->update($request->only('name', 'type', 'coverage_type', 'coverage_value', 'max_recipients', 'description', 'status'));
        return redirect()->route('finance.scholarships.index')->with('success', 'Scholarship updated successfully.');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        return redirect()->route('finance.scholarships.index')->with('success', 'Scholarship deleted.');
    }

    public function award(Request $request)
    {
        $request->validate([
            'scholarship_id' => 'required|exists:scholarships,id',
            'student_id'     => 'required|exists:students,id',
            'award_date'     => 'required|date',
        ]);

        ScholarshipAward::create([
            'scholarship_id' => $request->scholarship_id,
            'student_id'     => $request->student_id,
            'award_date'     => $request->award_date,
            'notes'          => $request->notes,
            'status'         => 'active',
            'awarded_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Scholarship awarded successfully.');
    }

    public function revoke(ScholarshipAward $award)
    {
        $award->update(['status' => 'suspended']);
        return back()->with('success', 'Scholarship award revoked.');
    }

    public function destroyAward(ScholarshipAward $award)
    {
        $award->delete();
        return back()->with('success', 'Scholarship award deleted.');
    }
}
