<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Timetable;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\Staff;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $semester = Semester::find($request->semester_id) ?? Semester::where('is_current', true)->first();
        $timetable = Timetable::whereHas('courseOffering', fn($q) => $q->where('semester_id', $semester?->id))
            ->with(['courseOffering.course', 'courseOffering.lecturer.user'])
            ->orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday')")
            ->orderBy('start_time')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $programs  = Program::active()->orderBy('name')->get();
        $offerings = CourseOffering::where('semester_id', $semester?->id)
            ->with(['course', 'lecturer.user'])->get();
        return view('academic.timetable.index', compact('timetable', 'semester', 'semesters', 'programs', 'offerings'));
    }

    public function create()
    {
        $semester = Semester::where('is_current', true)->first();
        $offerings = CourseOffering::where('semester_id', $semester?->id)->with(['course', 'lecturer'])->get();
        return view('academic.timetable.create', compact('offerings', 'semester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Timetable::create($request->except('_token'));
        return redirect()->route('academic.timetable.index')->with('success', 'Timetable entry added.');
    }

    public function show(Timetable $timetable)
    {
        return view('academic.timetable.show', compact('timetable'));
    }

    public function edit(Timetable $timetable)
    {
        $semester = Semester::where('is_current', true)->first();
        $offerings = CourseOffering::where('semester_id', $semester?->id)->with('course')->get();
        return view('academic.timetable.edit', compact('timetable', 'offerings'));
    }

    public function update(Request $request, Timetable $timetable)
    {
        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        $timetable->update($request->except('_token', '_method'));
        return redirect()->route('academic.timetable.index')->with('success', 'Timetable updated.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();
        return redirect()->route('academic.timetable.index')->with('success', 'Timetable entry deleted.');
    }

    public function viewSemester(Semester $semester)
    {
        return redirect()->route('academic.timetable.index', ['semester_id' => $semester->id]);
    }

    public function print()
    {
        $semester = Semester::where('is_current', true)->first();
        $timetables = Timetable::whereHas('courseOffering', fn($q) => $q->where('semester_id', $semester?->id))
            ->with(['courseOffering.course', 'courseOffering.lecturer'])
            ->orderByRaw("FIELD(day_of_week,'monday','tuesday','wednesday','thursday','friday','saturday')")
            ->orderBy('start_time')->get()->groupBy('day_of_week');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('academic.timetable.print', compact('timetables', 'semester'))->setPaper('a4', 'landscape');
        return $pdf->download("timetable_{$semester?->name}.pdf");
    }
}
