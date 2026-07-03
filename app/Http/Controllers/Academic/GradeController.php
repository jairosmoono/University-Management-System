<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\ExamResult;
use App\Models\Semester;
use App\Models\Staff;
use App\Models\Student;
use App\Models\CourseOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    private function lecturerOfferingIds(): ?array
    {
        if (!auth()->user()->hasRole('lecturer')) return null;
        $staff = Staff::where('user_id', auth()->id())->first();
        return CourseOffering::where('lecturer_id', $staff?->id)->pluck('id')->all();
    }

    public function index(Request $request)
    {
        $isLecturerView   = auth()->user()->hasRole('lecturer');
        $lecturerOffIds   = $this->lecturerOfferingIds();

        $query = Examination::with(['courseOffering.course', 'courseOffering.semester'])
            ->withCount('results as results_count');

        if ($isLecturerView) {
            $query->whereIn('course_offering_id', $lecturerOffIds ?? []);
        }
        if ($request->semester_id) {
            $query->whereHas('courseOffering', fn($q) => $q->where('semester_id', $request->semester_id));
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $examinations = $query->orderBy('exam_date', 'desc')->paginate(20);

        // Enrich each exam with enrolled count and avg marks
        $examinations->getCollection()->transform(function ($exam) {
            $exam->enrolled_count = Student::whereHas('courseRegistrations',
                fn($q) => $q->where('course_offering_id', $exam->course_offering_id)
                            ->where('status', 'registered')
            )->count();

            $exam->avg_marks = ExamResult::where('examination_id', $exam->id)
                ->where('is_absent', false)
                ->avg('marks_obtained');

            $exam->pass_count = ExamResult::where('examination_id', $exam->id)
                ->where('is_absent', false)
                ->whereRaw('marks_obtained >= ?', [$exam->passing_marks ?? ($exam->max_marks * 0.4)])
                ->count();

            return $exam;
        });

        // Stats
        $baseQ = Examination::query();
        if ($isLecturerView) $baseQ->whereIn('course_offering_id', $lecturerOffIds ?? []);

        $stats = [
            'total'       => (clone $baseQ)->count(),
            'graded'      => (clone $baseQ)->whereHas('results')->count(),
            'pending'     => (clone $baseQ)->whereDoesntHave('results')->count(),
            'this_month'  => (clone $baseQ)->whereMonth('exam_date', now()->month)->count(),
        ];

        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('academic.grades.index', compact('examinations', 'stats', 'semesters', 'isLecturerView'));
    }

    public function entry(Examination $examination)
    {
        // Lecturer ownership check
        if (auth()->user()->hasRole('lecturer')) {
            $offIds = $this->lecturerOfferingIds() ?? [];
            abort_if(!in_array($examination->course_offering_id, $offIds), 403, 'Not assigned to this course.');
        }

        $examination->load(['courseOffering.course', 'courseOffering.semester', 'invigilator.user']);

        // Load enrolled students with their existing result
        $students = Student::whereHas('courseRegistrations',
                fn($q) => $q->where('course_offering_id', $examination->course_offering_id)
                            ->where('status', 'registered'))
            ->with(['user', 'program',
                'examResults' => fn($q) => $q->where('examination_id', $examination->id)])
            ->orderBy('student_id')
            ->get();

        // Grade distribution for already-entered results
        $distribution = ExamResult::where('examination_id', $examination->id)
            ->select('grade', DB::raw('count(*) as cnt'))
            ->groupBy('grade')
            ->pluck('cnt', 'grade');

        $avgMark = ExamResult::where('examination_id', $examination->id)
            ->where('is_absent', false)
            ->avg('marks_obtained');

        return view('academic.grades.entry', compact('examination', 'students', 'distribution', 'avgMark'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'examination_id' => 'required|exists:examinations,id',
            'grades'         => 'required|array',
        ]);

        $examination = Examination::findOrFail($request->examination_id);

        if (auth()->user()->hasRole('lecturer')) {
            $offIds = $this->lecturerOfferingIds() ?? [];
            abort_if(!in_array($examination->course_offering_id, $offIds), 403);
        }

        DB::transaction(function () use ($request, $examination) {
            foreach ($request->grades as $studentId => $data) {
                $isAbsent = isset($data['is_absent']) && $data['is_absent'];
                $marks    = $isAbsent ? null : (floatval($data['marks'] ?? 0));
                $grade    = $isAbsent ? null : $this->calcGrade($marks, $examination->max_marks);

                ExamResult::updateOrCreate(
                    ['examination_id' => $examination->id, 'student_id' => $studentId],
                    [
                        'course_offering_id' => $examination->course_offering_id,
                        'marks_obtained'     => $marks,
                        'grade'              => $grade,
                        'is_absent'          => $isAbsent,
                        'remarks'            => $data['remarks'] ?? null,
                        'entered_by'         => auth()->id(),
                    ]
                );
            }
        });

        return back()->with('success', 'Grades saved successfully.');
    }

    public function update(Request $request, ExamResult $result)
    {
        if (auth()->user()->hasRole('lecturer')) {
            $offIds = $this->lecturerOfferingIds() ?? [];
            abort_if(!in_array($result->examination->course_offering_id, $offIds), 403);
        }

        $isAbsent = (bool) $request->is_absent;
        $marks    = $isAbsent ? null : floatval($request->marks_obtained ?? 0);
        $maxMarks = $result->examination->max_marks;

        $result->update([
            'marks_obtained' => $marks,
            'grade'          => $isAbsent ? null : $this->calcGrade($marks, $maxMarks),
            'is_absent'      => $isAbsent,
            'remarks'        => $request->remarks,
        ]);

        return back()->with('success', 'Grade updated.');
    }

    private function calcGrade(float $marks, float $maxMarks): string
    {
        if ($maxMarks <= 0) return 'F';
        $pct = ($marks / $maxMarks) * 100;
        return \App\Models\GradeScale::fromScore($pct)['grade'];
    }
}
