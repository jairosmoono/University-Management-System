<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseOfferingController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        $query = CourseOffering::with(['course.department', 'semester', 'lecturer'])->withCount('approvedRegistrations');
        if ($request->semester_id) $query->where('semester_id', $request->semester_id);
        if ($request->department_id) $query->whereHas('course', fn($q) => $q->where('department_id', $request->department_id));
        $offerings = $query->paginate(20);
        return view('academic.course-offerings.index', compact('offerings', 'semesters'));
    }

    private function studentIndex(Request $request, $semesters)
    {
        $student  = Student::where('user_id', auth()->id())->first();
        $semester = $request->semester_id
            ? Semester::find($request->semester_id)
            : Semester::where('is_current', true)->first();

        $query = CourseOffering::with(['course.department', 'semester', 'lecturer.user'])
            ->withCount('approvedRegistrations')
            ->where('status', 'active')
            ->where('semester_id', $semester?->id);

        if ($request->department_id) {
            $query->whereHas('course', fn($q) => $q->where('department_id', $request->department_id));
        }
        if ($request->search) {
            $query->whereHas('course', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"));
        }
        if ($request->filter === 'registered') {
            $query->whereHas('registrations', fn($q) => $q->where('student_id', $student?->id)->whereNotIn('status', ['dropped']));
        } elseif ($request->filter === 'available') {
            $query->whereDoesntHave('registrations', fn($q) => $q->where('student_id', $student?->id)->whereNotIn('status', ['dropped']));
        }

        $offerings = $query->paginate(20)->withQueryString();

        // Keyed by offering_id for O(1) lookup in the view
        $myRegistrations = CourseRegistration::where('student_id', $student?->id)
            ->whereHas('courseOffering', fn($q) => $q->where('semester_id', $semester?->id))
            ->get()
            ->keyBy('course_offering_id');

        $departments = Department::orderBy('name')->get();

        $stats = [
            'total'      => CourseOffering::where('semester_id', $semester?->id)->where('status', 'active')->count(),
            'registered' => $myRegistrations->whereNotIn('status', ['dropped'])->count(),
            'available'  => CourseOffering::where('semester_id', $semester?->id)->where('status', 'active')
                ->whereDoesntHave('registrations', fn($q) => $q->where('student_id', $student?->id)->whereNotIn('status', ['dropped']))
                ->count(),
        ];

        return view('academic.course-offerings.index', compact(
            'offerings', 'semesters', 'semester', 'myRegistrations',
            'departments', 'stats', 'student'
        ) + ['isStudentView' => true]);
    }

    public function create()
    {
        $courses   = Course::with('department')->active()->get();
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();
        $lecturers = Staff::with('user')->active()->get();
        return view('academic.course-offerings.create', compact('courses', 'semesters', 'lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'nullable|exists:staff,id',
            'max_students'=> 'required|integer|min:1',
            'venue'       => 'nullable|string|max:100',
            'schedule'    => 'nullable|string|max:255',
        ]);

        $semester = Semester::findOrFail($request->semester_id);

        try {
            CourseOffering::create([
                'course_id'       => $request->course_id,
                'semester_id'     => $request->semester_id,
                'academic_year_id'=> $semester->academic_year_id,
                'lecturer_id'     => $request->lecturer_id ?: null,
                'venue'           => $request->venue,
                'schedule'        => $request->schedule,
                'max_students'    => $request->max_students,
                'status'          => 'active',
            ]);
            return redirect()->route('academic.course-offerings.index')
                ->with('success', 'Course offering created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'This course is already offered in the selected semester.')->withInput();
        }
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load(['course', 'semester', 'lecturer', 'timetables', 'approvedRegistrations.student', 'examinations', 'assignments']);
        return view('academic.course-offerings.show', compact('courseOffering'));
    }

    public function edit(CourseOffering $courseOffering)
    {
        $courses   = Course::with('department')->active()->get();
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();
        $lecturers = Staff::with('user')->active()->get();
        return view('academic.course-offerings.edit', compact('courseOffering', 'courses', 'semesters', 'lecturers'));
    }

    public function update(Request $request, CourseOffering $courseOffering)
    {
        $request->validate([
            'course_id'   => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'nullable|exists:staff,id',
            'max_students'=> 'required|integer|min:1',
        ]);

        $semester = Semester::findOrFail($request->semester_id);

        $courseOffering->update([
            'course_id'       => $request->course_id,
            'semester_id'     => $request->semester_id,
            'academic_year_id'=> $semester->academic_year_id,
            'lecturer_id'     => $request->lecturer_id ?: null,
            'venue'           => $request->venue,
            'schedule'        => $request->schedule,
            'max_students'    => $request->max_students,
            'status'          => $request->status ?? $courseOffering->status,
        ]);
        return redirect()->route('academic.course-offerings.index')
            ->with('success', 'Course offering updated successfully.');
    }

    public function destroy(CourseOffering $courseOffering)
    {
        $courseOffering->delete();
        return redirect()->route('academic.course-offerings.index')
            ->with('success', 'Course offering deleted.');
    }

    public function ajaxBySemester(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer.user'])
            ->where('semester_id', $request->semester_id)
            ->where('status', 'active');

        if ($request->student_id) {
            $registered = \App\Models\CourseRegistration::where('student_id', $request->student_id)
                ->whereNotIn('status', ['dropped'])
                ->pluck('course_offering_id');
            $query->whereNotIn('id', $registered);
        }

        $offerings = $query->get()->map(fn($o) => [
            'id'          => $o->id,
            'course_code' => optional($o->course)->code,
            'course_name' => optional($o->course)->name,
            'enrolled'    => $o->enrolled_students,
            'max'         => $o->max_students,
            'lecturer'    => optional(optional($o->lecturer)->user)->name,
        ]);

        return response()->json($offerings);
    }
}
