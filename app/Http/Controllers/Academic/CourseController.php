<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePrerequisite;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    private function getCourseTypes(): array
    {
        if (Storage::exists('settings.json')) {
            $s = json_decode(Storage::get('settings.json'), true) ?? [];
            if (!empty($s['course_types'])) return $s['course_types'];
        }
        return ['core', 'elective', 'lab'];
    }

    public function index(Request $request)
    {
        $query = Course::with(['department.faculty'])->withCount('offerings');
        if ($request->department_id) $query->where('department_id', $request->department_id);
        if ($request->level) $query->where('level', $request->level);
        if ($request->course_type) $query->where('course_type', $request->course_type);
        if ($request->search) {
            $query->where(fn($q) => $q->where('courses.name', 'like', '%' . $request->search . '%')->orWhere('courses.code', 'like', '%' . $request->search . '%'));
        }
        $courses     = $query->orderBy('code')->paginate(20);
        $departments = Department::active()->get();
        $courseTypes = $this->getCourseTypes();
        return view('academic.courses.index', compact('courses', 'departments', 'courseTypes'));
    }

    public function create()
    {
        $departments = Department::with('faculty')->active()->get();
        $programs    = Program::active()->get();
        $courseTypes = $this->getCourseTypes();
        return view('academic.courses.create', compact('departments', 'programs', 'courseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:20|unique:courses,code',
            'credits'       => 'required|integer|min:1|max:10',
            'level'         => 'required',
        ]);

        $course = Course::create($request->only(['department_id', 'name', 'code', 'credits', 'level', 'course_type', 'description', 'prerequisites', 'status']));

        if ($request->programs) {
            foreach ($request->programs as $programId => $data) {
                $course->programs()->attach($programId, ['year_of_study' => $data['year'] ?? null]);
            }
        }
        return redirect()->route('academic.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['department.faculty', 'programs', 'offerings.lecturer.user', 'offerings.semester', 'prerequisites.prerequisiteCourse']);
        $allCourses = Course::active()->where('id', '!=', $course->id)->orderBy('code')->get();
        return view('academic.courses.show', compact('course', 'allCourses'));
    }

    public function addPrerequisite(Request $request, Course $course)
    {
        $request->validate([
            'prerequisite_course_id' => 'required|exists:courses,id|different:course_id',
            'min_grade'              => 'required|string|max:5',
        ]);

        if ($request->prerequisite_course_id == $course->id) {
            return back()->with('error', 'A course cannot be its own prerequisite.');
        }

        CoursePrerequisite::updateOrCreate(
            ['course_id' => $course->id, 'prerequisite_course_id' => $request->prerequisite_course_id],
            ['min_grade' => $request->min_grade]
        );

        return back()->with('success', 'Prerequisite added.');
    }

    public function removePrerequisite(Course $course, CoursePrerequisite $prerequisite)
    {
        abort_if($prerequisite->course_id !== $course->id, 403);
        $prerequisite->delete();
        return back()->with('success', 'Prerequisite removed.');
    }

    public function edit(Course $course)
    {
        $departments = Department::with('faculty')->active()->get();
        $programs    = Program::active()->get();
        $courseTypes = $this->getCourseTypes();
        return view('academic.courses.edit', compact('course', 'departments', 'programs', 'courseTypes'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:20|unique:courses,code,' . $course->id,
            'credits'       => 'required|integer|min:1|max:10',
        ]);

        $course->update($request->only(['department_id', 'name', 'code', 'credits', 'level', 'course_type', 'description', 'prerequisites', 'status']));
        return redirect()->route('academic.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('academic.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function byProgram(Program $program)
    {
        return response()->json($program->courses()->with('department')->active()->get(['courses.id', 'courses.name', 'courses.code', 'courses.credits', 'courses.level']));
    }
}
