<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\ExamType;
use App\Models\CourseOffering;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    private function examTypes()
    {
        return ExamType::active()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $query = Examination::with(['courseOffering.course', 'courseOffering.semester', 'invigilator']);
        if ($request->semester_id) $query->whereHas('courseOffering', fn($q) => $q->where('semester_id', $request->semester_id));
        if ($request->type) $query->where('type', $request->type);
        $examinations = $query->orderBy('exam_date')->paginate(20);
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $semester  = Semester::where('is_current', true)->first();
        $offerings = CourseOffering::where('semester_id', $semester?->id)->with('course')->get();
        $staff     = \App\Models\Staff::with('user')->active()->get();
        $examTypes = $this->examTypes();
        return view('academic.examinations.index', compact('examinations', 'semesters', 'offerings', 'staff', 'examTypes'));
    }

    public function create()
    {
        $semester  = Semester::where('is_current', true)->first();
        $offerings = CourseOffering::where('semester_id', $semester?->id)->with('course')->get();
        $staff     = \App\Models\Staff::with('user')->active()->get();
        $examTypes = $this->examTypes();
        return view('academic.examinations.create', compact('offerings', 'staff', 'semester', 'examTypes'));
    }

    public function store(Request $request)
    {
        $validCodes = ExamType::active()->pluck('code')->toArray();
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:' . implode(',', $validCodes),
            'exam_date'          => 'required|date',
            'start_time'         => 'required',
            'end_time'           => 'required',
            'max_marks'          => 'required|numeric|min:1',
        ]);

        Examination::create($request->only([
            'course_offering_id', 'name', 'type', 'exam_date',
            'start_time', 'end_time', 'venue', 'max_marks', 'passing_marks', 'invigilator_id', 'status',
        ]));
        return redirect()->route('academic.examinations.index')->with('success', 'Examination scheduled successfully.');
    }

    public function show(Examination $examination)
    {
        $examination->load(['courseOffering.course', 'courseOffering.lecturer', 'results.student', 'invigilator']);
        return view('academic.examinations.show', compact('examination'));
    }

    public function edit(Examination $examination)
    {
        $offerings = CourseOffering::with('course')->get();
        $staff     = \App\Models\Staff::with('user')->active()->get();
        $examTypes = $this->examTypes();
        return view('academic.examinations.edit', compact('examination', 'offerings', 'staff', 'examTypes'));
    }

    public function update(Request $request, Examination $examination)
    {
        $validCodes = ExamType::active()->pluck('code')->toArray();
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'name'               => 'required|string|max:255',
            'type'               => 'required|in:' . implode(',', $validCodes),
            'exam_date'          => 'required|date',
            'start_time'         => 'required',
            'end_time'           => 'required',
        ]);

        $examination->update($request->only([
            'course_offering_id', 'name', 'type', 'exam_date',
            'start_time', 'end_time', 'venue', 'max_marks', 'passing_marks', 'invigilator_id', 'status',
        ]));

        return redirect()->route('academic.examinations.index')->with('success', 'Examination updated.');
    }

    public function destroy(Examination $examination)
    {
        $examination->delete();
        return redirect()->route('academic.examinations.index')->with('success', 'Examination deleted.');
    }

    public function seatingPlan(Examination $examination)
    {
        $examination->load(['courseOffering.course', 'courseOffering.semester', 'invigilator.user']);

        $courseId = $examination->courseOffering?->course_id;

        // 1. Students with a course registration for this offering
        $students = Student::whereHas('courseRegistrations', fn($q) =>
                $q->where('course_offering_id', $examination->course_offering_id)
                  ->whereNotIn('status', ['dropped', 'failed'])
            )
            ->with(['user', 'program'])
            ->orderBy('student_id')
            ->get();

        // 2. Fallback: students in programs that have this course via course_program pivot
        if ($students->isEmpty() && $courseId) {
            $programIds = \Illuminate\Support\Facades\DB::table('course_program')
                ->where('course_id', $courseId)
                ->pluck('program_id');

            if ($programIds->isNotEmpty()) {
                $students = Student::whereIn('program_id', $programIds)
                    ->where('status', 'active')
                    ->with(['user', 'program'])
                    ->orderBy('student_id')
                    ->get();
            }
        }

        // 3. Fallback: all active students in the department of the course
        if ($students->isEmpty() && $courseId) {
            $departmentId = \App\Models\Course::find($courseId)?->department_id;
            if ($departmentId) {
                $students = Student::whereHas('program', fn($q) => $q->where('department_id', $departmentId))
                    ->where('status', 'active')
                    ->with(['user', 'program'])
                    ->orderBy('student_id')
                    ->get();
            }
        }

        // 4. Ultimate fallback: all active students
        if ($students->isEmpty()) {
            $students = Student::where('status', 'active')
                ->with(['user', 'program'])
                ->orderBy('student_id')
                ->get();
        }

        $examName = (string) $examination->name;
        $examDate = \Carbon\Carbon::parse($examination->exam_date)->format('d M Y');

        $typeName = ExamType::where('code', $examination->type)->value('name')
                  ?? ucwords(str_replace('_', ' ', $examination->type ?? ''));

        $settingsPath = storage_path('app/settings.json');
        $settings = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?? []) : [];
        $uniName  = $settings['university_name'] ?? config('app.name', 'University');
        $uniAddr  = $settings['university_address'] ?? '';
        $uniPhone = $settings['university_phone'] ?? '';
        $uniEmail = $settings['university_email'] ?? '';
        $logoSrc  = null;
        if (!empty($settings['logo_path'])) {
            $lf = storage_path('app/public/' . $settings['logo_path']);
            if (file_exists($lf)) {
                $logoSrc = 'data:' . mime_content_type($lf) . ';base64,' . base64_encode(file_get_contents($lf));
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('academic.examinations.seating',
            compact('examination', 'examName', 'examDate', 'students', 'typeName', 'uniName', 'uniAddr', 'uniPhone', 'uniEmail', 'logoSrc'))
            ->setPaper('a4');

        return $pdf->download("seating_plan_{$examination->id}.pdf");
    }
}
