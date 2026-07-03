<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('student')) {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            return $this->studentReport($student);
        }

        $today    = now()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $weekEnd   = now()->endOfWeek()->format('Y-m-d');

        $stats = [
            'today_sessions' => AttendanceSession::whereDate('date', $today)->count(),
            'today_present'  => AttendanceRecord::whereHas('session', fn($q) => $q->whereDate('date', $today))->where('status', 'present')->count(),
            'today_absent'   => AttendanceRecord::whereHas('session', fn($q) => $q->whereDate('date', $today))->where('status', 'absent')->count(),
            'week_sessions'  => AttendanceSession::whereBetween('date', [$weekStart, $weekEnd])->count(),
        ];

        $sessionsQuery = DB::table('attendance_sessions as asess')
            ->leftJoin('programs as p', 'p.id', '=', 'asess.program_id')
            ->leftJoin('course_offerings as co', 'co.id', '=', 'asess.course_offering_id')
            ->leftJoin('courses as c', 'c.id', '=', 'co.course_id')
            ->leftJoin('attendance_records as ar', 'ar.attendance_session_id', '=', 'asess.id')
            ->select(
                'asess.id', 'asess.date', 'asess.session_type', 'asess.topic',
                'p.name as program_name', 'p.code as program_code',
                'c.code as course_code', 'c.name as course_name',
                DB::raw('SUM(CASE WHEN ar.status = "present" THEN 1 ELSE 0 END) as present_count'),
                DB::raw('SUM(CASE WHEN ar.status = "absent"  THEN 1 ELSE 0 END) as absent_count'),
                DB::raw('SUM(CASE WHEN ar.status = "late"    THEN 1 ELSE 0 END) as late_count'),
                DB::raw('COUNT(ar.id) as total_count')
            )
            ->groupBy('asess.id', 'asess.date', 'asess.session_type', 'asess.topic',
                      'p.name', 'p.code', 'c.code', 'c.name')
            ->orderBy('asess.date', 'desc');

        if ($request->program_id) {
            $sessionsQuery->where('asess.program_id', $request->program_id);
        }
        if ($request->date_from) {
            $sessionsQuery->where('asess.date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $sessionsQuery->where('asess.date', '<=', $request->date_to);
        }

        $sessions = $sessionsQuery->paginate(20)->withQueryString();
        $programs = Program::active()->orderBy('name')->get();

        return view('academic.attendance.index', compact('stats', 'sessions', 'programs'));
    }

    public function take(CourseOffering $offering)
    {
        $students = Student::whereHas('courseRegistrations', fn($q) =>
                $q->where('course_offering_id', $offering->id)
                  ->whereNotIn('status', ['dropped', 'failed'])
            )
            ->with('user')
            ->orderBy('student_id')
            ->get();

        $today = now()->format('Y-m-d');
        $existingSession = AttendanceSession::where('course_offering_id', $offering->id)
            ->where('date', $today)->first();

        return view('academic.attendance.take', compact('offering', 'students', 'today', 'existingSession'));
    }

    public function takeByProgram(Program $program, CourseOffering $offering)
    {
        $today = now()->format('Y-m-d');

        $existingSession = AttendanceSession::where('program_id', $program->id)
            ->where('course_offering_id', $offering->id)
            ->where('date', $today)
            ->first();

        if ($existingSession) {
            return redirect()->route('academic.attendance.index')
                ->with('error', "Attendance for {$program->name} — {$offering->course?->name} has already been taken today.");
        }

        // Students in this program who are registered for this offering
        $students = Student::where('program_id', $program->id)
            ->whereHas('courseRegistrations', fn($q) =>
                $q->where('course_offering_id', $offering->id)
                  ->whereNotIn('status', ['dropped', 'failed'])
            )
            ->with('user')
            ->orderBy('student_id')
            ->get();

        // Fallback: all active students in the program
        if ($students->isEmpty()) {
            $students = Student::where('program_id', $program->id)
                ->where('status', 'active')
                ->with('user')
                ->orderBy('student_id')
                ->get();
        }

        $offering->load('course');

        return view('academic.attendance.take-by-program', compact('program', 'offering', 'students', 'today'));
    }

    public function offeringsByProgram(Program $program)
    {
        $semester = Semester::where('is_current', true)->first();

        $offeringMap = CourseOffering::when($semester, fn($q) => $q->where('semester_id', $semester->id))
            ->pluck('id', 'course_id');

        // 1. Try courses linked via course_program pivot
        $courseIds = DB::table('course_program')->where('program_id', $program->id)->pluck('course_id');

        // 2. Fallback: courses in the same department as the program
        if ($courseIds->isEmpty() && $program->department_id) {
            $courseIds = \App\Models\Course::where('department_id', $program->department_id)
                ->where('status', 'active')
                ->pluck('id');
        }

        // 3. Fallback: all courses that have an offering this semester
        if ($courseIds->isEmpty()) {
            $courseIds = $offeringMap->keys();
        }

        $courses = \App\Models\Course::whereIn('id', $courseIds)
            ->where('status', 'active')
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn($c) => [
                'offering_id' => $offeringMap[$c->id] ?? null,
                'code'        => $c->code,
                'name'        => $c->name,
            ])
            ->filter(fn($c) => $c['offering_id'] !== null)
            ->values();

        return response()->json($courses);
    }

    public function save(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'nullable|exists:course_offerings,id',
            'program_id'         => 'nullable|exists:programs,id',
            'date'               => 'required|date',
            'attendance'         => 'required|array',
        ]);

        $sessionData = [
            'date'         => $request->date,
            'session_type' => $request->type ?? 'lecture',
        ];
        if ($request->course_offering_id) {
            $sessionData['course_offering_id'] = $request->course_offering_id;
        }
        if ($request->program_id) {
            $sessionData['program_id'] = $request->program_id;
        }

        $session = AttendanceSession::firstOrCreate($sessionData, [
            'topic'      => $request->topic,
            'created_by' => auth()->user()->staff?->id,
        ]);

        foreach ($request->attendance as $studentId => $status) {
            AttendanceRecord::updateOrCreate(
                ['attendance_session_id' => $session->id, 'student_id' => $studentId],
                ['status' => $status, 'remarks' => $request->remarks[$studentId] ?? null]
            );
        }

        return redirect()->route('academic.attendance.index')
            ->with('success', 'Attendance recorded successfully.');
    }

    public function report(Request $request)
    {
        $query = CourseOffering::with(['course', 'semester', 'lecturer']);
        if ($request->semester_id) $query->where('semester_id', $request->semester_id);
        $offerings = $query->get()->map(function($offering) {
            $totalSessions = $offering->attendanceSessions()->count();
            $offering->total_sessions = $totalSessions;
            return $offering;
        });
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('academic.attendance.report', compact('offerings', 'semesters'));
    }

    public function studentReport(Student $student)
    {
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $semester  = request('semester_id')
            ? Semester::find(request('semester_id'))
            : Semester::where('is_current', true)->first();

        $isSelfView = auth()->user()->hasRole('student');

        $courseOfferings = CourseOffering::whereHas('registrations',
                fn($q) => $q->where('student_id', $student->id)->whereNotIn('status', ['dropped'])
            )
            ->where('semester_id', $semester?->id)
            ->with(['course', 'attendanceSessions' => function ($q) use ($student) {
                $q->orderBy('date')->with(['records' => fn($r) => $r->where('student_id', $student->id)]);
            }])
            ->get();

        $courseStats = $courseOfferings->map(function ($offering) {
            $sessions = $offering->attendanceSessions;
            $total    = $sessions->count();
            $present  = $sessions->filter(fn($s) => $s->records->first()?->status === 'present')->count();
            $absent   = $sessions->filter(fn($s) => $s->records->first()?->status === 'absent')->count();
            $late     = $sessions->filter(fn($s) => $s->records->first()?->status === 'late')->count();
            $rate     = $total > 0 ? round(($present / $total) * 100) : null;

            return [
                'offering'  => $offering,
                'total'     => $total,
                'present'   => $present,
                'absent'    => $absent,
                'late'      => $late,
                'rate'      => $rate,
            ];
        });

        $totalSessions = $courseStats->sum('total');
        $totalPresent  = $courseStats->sum('present');
        $totalAbsent   = $courseStats->sum('absent');
        $totalLate     = $courseStats->sum('late');
        $overallRate   = $totalSessions > 0 ? round(($totalPresent / $totalSessions) * 100) : null;

        return view('academic.attendance.student', compact(
            'student', 'semester', 'semesters', 'courseStats',
            'totalSessions', 'totalPresent', 'totalAbsent', 'totalLate', 'overallRate',
            'isSelfView'
        ));
    }
}
