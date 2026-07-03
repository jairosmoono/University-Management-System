<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Examination;
use App\Models\ExamResult;
use App\Models\FinalResult;
use App\Models\CourseOffering;
use App\Models\Notification;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Semester;
use App\Models\GpaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    private function lecturerStaff(): ?Staff
    {
        return Staff::where('user_id', auth()->id())->first();
    }

    private function assertLecturerOwns(CourseOffering $offering): void
    {
        if (auth()->user()->hasRole('lecturer')) {
            abort_if(
                $offering->lecturer_id !== $this->lecturerStaff()?->id,
                403,
                'You are not assigned to this course offering.'
            );
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            return $this->studentResults($student);
        }

        $isLecturerView = $user->hasRole('lecturer');
        $staff          = $isLecturerView ? $this->lecturerStaff() : null;

        $resultsQuery = FinalResult::with(['student.user', 'courseOffering.course', 'courseOffering.semester']);

        if ($isLecturerView) {
            $resultsQuery->whereHas('courseOffering', fn($q) => $q->where('lecturer_id', $staff?->id));
        }
        if ($request->semester_id) {
            $resultsQuery->whereHas('courseOffering', fn($q) => $q->where('semester_id', $request->semester_id));
        }
        if ($request->offering_id) {
            $resultsQuery->where('course_offering_id', $request->offering_id);
        }
        if ($request->academic_year_id) {
            $resultsQuery->whereHas('courseOffering.semester', fn($q) => $q->where('academic_year_id', $request->academic_year_id));
        }

        $results = $resultsQuery->latest()->paginate(30);

        // GPA Records
        $gpaQuery = GpaRecord::with(['student.user', 'student.program', 'semester.academicYear']);
        if ($request->gpa_semester_id) $gpaQuery->where('semester_id', $request->gpa_semester_id);
        if ($request->gpa_standing)    $gpaQuery->where('academic_standing', $request->gpa_standing);
        if ($request->gpa_program_id) {
            $gpaQuery->whereHas('student', fn($q) => $q->where('program_id', $request->gpa_program_id));
        }
        if ($request->gpa_search) {
            $s = $request->gpa_search;
            $gpaQuery->whereHas('student', fn($q) =>
                $q->where('student_id', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
            );
        }

        $gpaStats = [
            'total'      => (clone $gpaQuery)->count(),
            'avg_gpa'    => round((clone $gpaQuery)->avg('gpa') ?? 0, 2),
            'avg_cgpa'   => round((clone $gpaQuery)->avg('cgpa') ?? 0, 2),
            'deans_list' => (clone $gpaQuery)->where('academic_standing', "Dean's List")->count(),
            'probation'  => (clone $gpaQuery)->whereIn('academic_standing', ['Probation', 'Academic Dismissal'])->count(),
        ];

        $gpaRecords    = $gpaQuery->latest()->paginate(25, ['*'], 'gpa_page');
        $semesters     = Semester::orderBy('start_date', 'desc')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $programs      = Program::active()->orderBy('name')->get();

        // Offerings dropdown — lecturer sees only their courses
        $offerings = $isLecturerView
            ? CourseOffering::where('lecturer_id', $staff?->id)->with('course', 'semester')->get()
            : CourseOffering::with('course')->get();

        return view('academic.results.index', compact(
            'results', 'gpaRecords', 'gpaStats',
            'semesters', 'academicYears', 'offerings', 'programs',
            'isLecturerView'
        ));
    }

    public function entry(CourseOffering $offering)
    {
        $this->assertLecturerOwns($offering);

        $offering->load(['course', 'semester', 'lecturer.user']);

        // Load all examinations for this offering grouped by type
        $examinations = Examination::where('course_offering_id', $offering->id)->get();
        $suppExams    = $examinations->whereIn('type', ['assessment', 'practical', 'quiz'])->values();
        $midtermExams = $examinations->where('type', 'mid_term')->values();
        $finalExams   = $examinations->where('type', 'end_of_term')->values();

        // Pre-fetch all exam results keyed by [student_id][examination_id]
        $rawResults = ExamResult::whereIn('examination_id', $examinations->pluck('id'))->get();

        // Build lookup: $erMap[student_id][examination_id] = ExamResult
        $erMap = [];
        foreach ($rawResults as $er) {
            $erMap[$er->student_id][$er->examination_id] = $er;
        }

        $students = Student::whereHas('courseRegistrations', fn($q) =>
                $q->where('course_offering_id', $offering->id)->where('status', 'registered'))
            ->with(['user', 'finalResults' => fn($q) => $q->where('course_offering_id', $offering->id)])
            ->orderBy('student_id')
            ->get();

        // Build per-student score calculations as plain arrays
        $calcScores = [];
        foreach ($students as $student) {
            $sid = $student->id;
            $studentResults = $erMap[$sid] ?? [];

            $suppTotal = 0;
            foreach ($suppExams as $exam) {
                $er = $studentResults[$exam->id] ?? null;
                $suppTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
            }

            $midTotal = 0;
            foreach ($midtermExams as $exam) {
                $er = $studentResults[$exam->id] ?? null;
                $midTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
            }

            $finalTotal = 0;
            foreach ($finalExams as $exam) {
                $er = $studentResults[$exam->id] ?? null;
                $finalTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
            }

            $calcScores[$sid] = [
                'calc_ca'   => ($suppTotal + $midTotal) > 0 ? round(($suppTotal + $midTotal) / 300 * 40, 2) : null,
                'calc_exam' => $finalTotal > 0              ? round($finalTotal / 100 * 60, 2)               : null,
            ];
        }

        return view('academic.results.entry', compact(
            'offering', 'students',
            'suppExams', 'midtermExams', 'finalExams',
            'erMap', 'calcScores'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'         => 'required|exists:students,id',
            'course_offering_id' => 'required|exists:course_offerings,id',
            'ca_score'           => 'required|numeric|min:0|max:40',
            'exam_score'         => 'required|numeric|min:0|max:60',
        ]);

        $offering = CourseOffering::with('semester')->findOrFail($request->course_offering_id);
        $this->assertLecturerOwns($offering);

        $caScore     = floatval($request->ca_score);
        $examScore   = floatval($request->exam_score);
        $total       = $caScore + $examScore;
        $grade       = scoreToGrade($total);
        $gradePoints = gradeToPoints($grade);

        FinalResult::updateOrCreate(
            ['student_id' => $request->student_id, 'course_offering_id' => $request->course_offering_id],
            [
                'academic_year_id' => $offering->semester?->academic_year_id,
                'semester_id'      => $offering->semester_id,
                'ca_score'         => $caScore,
                'exam_score'       => $examScore,
                'total_score'      => $total,
                'grade'            => $grade,
                'grade_points'     => $gradePoints,
                'status'           => 'pending',
            ]
        );

        return back()->with('success', "Result saved — {$grade} ({$total}).");
    }

    public function save(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'results'            => 'required|array',
        ]);

        $offering = CourseOffering::with('semester')->findOrFail($request->course_offering_id);
        $this->assertLecturerOwns($offering);

        DB::transaction(function () use ($request, $offering) {
            foreach ($request->results as $studentId => $data) {
                $caTotal   = floatval($data['ca_total'] ?? 0);
                $examScore = floatval($data['exam_score'] ?? 0);
                $total     = $caTotal + $examScore;
                $grade     = scoreToGrade($total);

                FinalResult::updateOrCreate(
                    ['student_id' => $studentId, 'course_offering_id' => $offering->id],
                    [
                        'academic_year_id' => $offering->semester?->academic_year_id,
                        'semester_id'      => $offering->semester_id,
                        'ca_score'         => $caTotal,
                        'exam_score'       => $examScore,
                        'total_score'      => $total,
                        'grade'            => $grade,
                        'grade_points'     => gradeToPoints($grade),
                        'status'           => 'pending',
                    ]
                );
            }
        });

        return back()->with('success', 'Results saved. Awaiting approval.');
    }

    public function update(Request $request, FinalResult $result)
    {
        $this->assertLecturerOwns($result->courseOffering);

        abort_if(
            $result->status !== 'pending' && auth()->user()->hasRole('lecturer'),
            403,
            'Approved results cannot be edited.'
        );

        $caScore     = floatval($request->ca_score ?? $result->ca_score);
        $examScore   = floatval($request->exam_score ?? $result->exam_score);
        $total       = $caScore + $examScore;
        $grade       = scoreToGrade($total);

        $result->update([
            'ca_score'     => $caScore,
            'exam_score'   => $examScore,
            'total_score'  => $total,
            'grade'        => $grade,
            'grade_points' => gradeToPoints($grade),
        ]);

        return back()->with('success', 'Result updated.');
    }

    public function approve(FinalResult $result)
    {
        abort_if(auth()->user()->hasRole('lecturer'), 403, 'Lecturers cannot approve results.');

        $status = ($result->grade === 'F') ? 'fail' : 'pass';
        $result->update(['status' => $status]);
        $result->load(['courseOffering.course', 'student.user']);
        $this->recalculateGpa($result->student_id, $result->courseOffering->semester_id);

        if ($userId = optional(optional($result->student)->user)->id) {
            $course = optional(optional($result->courseOffering)->course)->name ?? 'a course';
            Notification::send(
                $userId, 'result',
                'Result Published',
                "Your result for {$course} has been published. Grade: {$result->grade}.",
                [], route('academic.results.student', $result->student_id)
            );
        }

        return back()->with('success', 'Result approved and GPA updated.');
    }

    public function generateFromGrades(Request $request)
    {
        $offeringId = $request->offering_id; // optional — null = all

        // Get all offerings that have exam_results
        $offeringsWithGrades = CourseOffering::whereHas('examinations.results')
            ->when($offeringId, fn($q) => $q->where('id', $offeringId))
            ->with(['semester', 'examinations.results'])
            ->get();

        if ($offeringsWithGrades->isEmpty()) {
            return back()->with('error', 'No grades found to generate results from.');
        }

        $saved = 0;

        DB::transaction(function () use ($offeringsWithGrades, &$saved) {
            foreach ($offeringsWithGrades as $offering) {
                $examinations = $offering->examinations;
                $suppExams    = $examinations->whereIn('type', ['assessment', 'practical', 'quiz']);
                $midtermExams = $examinations->where('type', 'mid_term');
                $finalExams   = $examinations->where('type', 'end_of_term');

                // Build erMap[student_id][exam_id] for this offering
                $examIds = $examinations->pluck('id');
                $erMap   = [];
                foreach (ExamResult::whereIn('examination_id', $examIds)->get() as $er) {
                    $erMap[$er->student_id][$er->examination_id] = $er;
                }

                // Get all registered students for this offering
                $students = Student::whereHas('courseRegistrations',
                    fn($q) => $q->where('course_offering_id', $offering->id)
                                ->where('status', 'registered')
                )->get();

                foreach ($students as $student) {
                    $sid     = $student->id;
                    $sResults = $erMap[$sid] ?? [];

                    $suppTotal = 0;
                    foreach ($suppExams as $exam) {
                        $er = $sResults[$exam->id] ?? null;
                        $suppTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
                    }

                    $midTotal = 0;
                    foreach ($midtermExams as $exam) {
                        $er = $sResults[$exam->id] ?? null;
                        $midTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
                    }

                    $finalTotal = 0;
                    foreach ($finalExams as $exam) {
                        $er = $sResults[$exam->id] ?? null;
                        $finalTotal += ($er && !$er->is_absent) ? (float) $er->marks_obtained : 0;
                    }

                    // Only generate if at least one score component exists
                    if ($suppTotal + $midTotal + $finalTotal <= 0) continue;

                    $caScore   = round(($suppTotal + $midTotal) / 300 * 40, 2);
                    $examScore = round($finalTotal / 100 * 60, 2);
                    $total     = $caScore + $examScore;
                    $grade     = scoreToGrade($total);

                    FinalResult::updateOrCreate(
                        ['student_id' => $sid, 'course_offering_id' => $offering->id],
                        [
                            'academic_year_id' => $offering->semester?->academic_year_id,
                            'semester_id'      => $offering->semester_id,
                            'ca_score'         => $caScore,
                            'exam_score'       => $examScore,
                            'total_score'      => $total,
                            'grade'            => $grade,
                            'grade_points'     => gradeToPoints($grade),
                            'status'           => 'pending',
                        ]
                    );
                    $saved++;
                }
            }
        });

        return back()->with('success', "Generated {$saved} final result(s) from grades.");
    }

    public function calculateGpa(Request $request)
    {
        $semesterId = $request->semester_id ?: null;

        // Get all unique student–semester pairs that have approved results
        $pairs = FinalResult::whereIn('status', ['pass', 'fail'])
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->whereNotNull('semester_id')
            ->select('student_id', 'semester_id')
            ->distinct()
            ->get();

        if ($pairs->isEmpty()) {
            return back()->with('error', 'No approved results found' . ($semesterId ? ' for the selected semester.' : '.') . ' Approve results first, then calculate GPA.');
        }

        foreach ($pairs as $pair) {
            $this->recalculateGpa($pair->student_id, $pair->semester_id);
        }

        $studentCount  = $pairs->pluck('student_id')->unique()->count();
        $semesterCount = $pairs->pluck('semester_id')->unique()->count();

        return back()->with('success', "GPA recalculated for {$studentCount} student(s) across {$semesterCount} semester(s)/term(s).");
    }

    public function studentResults(Student $student)
    {
        $isSelfView = auth()->user()->hasRole('student');
        $semesters  = Semester::orderBy('start_date', 'desc')->get();

        $allResults = FinalResult::where('student_id', $student->id)
            ->with(['courseOffering.course', 'courseOffering.semester.academicYear'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by semester_id for display
        $resultsBySemester = $allResults->groupBy('semester_id');

        // GPA records keyed by semester_id
        $gpaRecords = GpaRecord::where('student_id', $student->id)
            ->with('semester.academicYear')
            ->orderBy('created_at', 'desc')
            ->get()
            ->keyBy('semester_id');

        // Summary stats
        $latestGpa = $gpaRecords->first();
        $stats = [
            'total'    => $allResults->count(),
            'passed'   => $allResults->where('status', 'pass')->count(),
            'failed'   => $allResults->where('status', 'fail')->count(),
            'pending'  => $allResults->where('status', 'pending')->count(),
            'cgpa'     => $latestGpa?->cgpa,
            'standing' => $latestGpa?->academic_standing,
        ];

        return view('academic.results.student', compact(
            'student', 'resultsBySemester', 'gpaRecords',
            'semesters', 'stats', 'isSelfView'
        ));
    }

    public function recalculateStudentGpa(Student $student, Semester $semester)
    {
        $this->recalculateGpa($student->id, $semester->id);
        return back()->with('success', "GPA recalculated for {$student->student_id}.");
    }

    public function gpaReport(Student $student)
    {
        $gpaRecords = GpaRecord::where('student_id', $student->id)->with('semester.academicYear')->orderBy('created_at')->get();
        return view('academic.results.gpa', compact('student', 'gpaRecords'));
    }

    public function recalculateGpa(int $studentId, int $semesterId)
    {
        $semesterResults = FinalResult::where('student_id', $studentId)
            ->whereHas('courseOffering', fn($q) => $q->where('semester_id', $semesterId))
            ->whereIn('status', ['pass', 'fail'])
            ->with('courseOffering.course')->get();

        if ($semesterResults->isEmpty()) return;

        $totalCredits = 0;
        $totalPoints  = 0;
        foreach ($semesterResults as $result) {
            $credits       = $result->courseOffering->course->credits ?? 0;
            $totalCredits += $credits;
            $totalPoints  += $result->grade_points * $credits;
        }

        $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;

        $allResults = FinalResult::where('student_id', $studentId)
            ->whereIn('status', ['pass', 'fail'])
            ->with('courseOffering.course')->get();
        $allCredits = $allResults->sum(fn($r) => $r->courseOffering->course->credits ?? 0);
        $allPoints  = $allResults->sum(fn($r) => $r->grade_points * ($r->courseOffering->course->credits ?? 0));
        $cgpa       = $allCredits > 0 ? round($allPoints / $allCredits, 2) : 0;

        $standing = 'Good Standing';
        if ($cgpa < 1.0)      $standing = 'Academic Dismissal';
        elseif ($cgpa < 2.0)  $standing = 'Probation';
        elseif ($cgpa >= 3.5) $standing = "Dean's List";

        GpaRecord::updateOrCreate(
            ['student_id' => $studentId, 'semester_id' => $semesterId],
            [
                'academic_year_id'     => Semester::find($semesterId)?->academic_year_id,
                'gpa'                  => $gpa,
                'cgpa'                 => $cgpa,
                'credits_earned'       => $totalCredits,
                'total_credits_earned' => $allCredits,
                'academic_standing'    => $standing,
            ]
        );
    }
}
