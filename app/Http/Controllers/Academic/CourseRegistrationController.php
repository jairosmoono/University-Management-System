<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\CourseRegistration;
use App\Models\CourseOffering;
use App\Models\CourseWaitlist;
use App\Models\FinalResult;
use App\Models\Notification;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    private function registrationOpen(): bool
    {
        $path = storage_path('app/settings.json');
        $settings = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
        return $settings['registration_open'] ?? true;
    }

    public function index(Request $request)
    {
        $user            = auth()->user();
        $isLecturerView  = $user->hasRole('lecturer');
        $isStudentView   = $user->hasRole('student');
        $lecturerOfferingIds = null;
        $myStudent = null;

        if ($isLecturerView) {
            $staff = Staff::where('user_id', $user->id)->first();
            $lecturerOfferingIds = CourseOffering::where('lecturer_id', $staff?->id)->pluck('id');
        }

        if ($isStudentView) {
            $myStudent = Student::where('user_id', $user->id)->first();
        }

        $query = CourseRegistration::with(['student.user', 'student.program', 'courseOffering.course', 'courseOffering.semester', 'courseOffering.lecturer.user']);

        if ($isLecturerView) {
            $query->whereIn('course_offering_id', $lecturerOfferingIds ?? []);
        }

        if ($isStudentView) {
            $query->where('student_id', $myStudent?->id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->semester_id) {
            $query->whereHas('courseOffering', fn($q) => $q->where('semester_id', $request->semester_id));
        }
        if ($request->offering_id) {
            $query->where('course_offering_id', $request->offering_id);
        }
        if (!$isStudentView) {
            if ($request->program_id) {
                $query->whereHas('student', fn($q) => $q->where('program_id', $request->program_id));
            }
            if ($request->search) {
                $search = $request->search;
                $query->whereHas('student', function($q) use ($search) {
                    $q->where('student_id', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            }
        }

        $registrations = $query->latest()->paginate(25);
        $semesters     = Semester::orderBy('start_date', 'desc')->get();
        $programs      = Program::active()->orderBy('name')->get();

        $offeringsForFilter = $isLecturerView
            ? CourseOffering::whereIn('id', $lecturerOfferingIds ?? [])->with('course')->get()
            : collect();

        $baseQuery = match(true) {
            $isStudentView  => CourseRegistration::where('student_id', $myStudent?->id),
            $isLecturerView => CourseRegistration::whereIn('course_offering_id', $lecturerOfferingIds ?? []),
            default         => CourseRegistration::query(),
        };

        $stats = [
            'total'      => (clone $baseQuery)->distinct()->count('course_offering_id'),
            'registered' => (clone $baseQuery)->where('status', 'registered')->distinct()->count('student_id'),
            'dropped'    => (clone $baseQuery)->where('status', 'dropped')->count(),
            'completed'  => (clone $baseQuery)->where('status', 'completed')->count(),
            'failed'     => (clone $baseQuery)->where('status', 'failed')->count(),
        ];

        // Admin view: one row per student with subject counts
        $studentList = null;
        if (!$isStudentView && !$isLecturerView) {
            $sq = Student::with(['user', 'program'])
                ->withCount([
                    'courseRegistrations as total_subjects',
                    'courseRegistrations as registered_count' => fn($q) => $q->where('status', 'registered'),
                    'courseRegistrations as dropped_count'    => fn($q) => $q->where('status', 'dropped'),
                ])
                ->has('courseRegistrations');

            if ($request->program_id) {
                $sq->where('program_id', $request->program_id);
            }
            if ($request->search) {
                $search = $request->search;
                $sq->where(fn($q) => $q->where('student_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")));
            }
            if ($request->semester_id) {
                $sq->whereHas('courseRegistrations.courseOffering',
                    fn($q) => $q->where('semester_id', $request->semester_id));
            }
            $studentList = $sq->orderBy('student_id')->paginate(25);
        }

        $registrationOpen = $this->registrationOpen();

        return view('academic.registrations.index', compact(
            'registrations', 'studentList', 'semesters', 'programs', 'stats',
            'isLecturerView', 'isStudentView', 'offeringsForFilter', 'myStudent', 'registrationOpen'
        ));
    }

    public function register(Request $request)
    {
        if (auth()->user()->hasRole('student')) {
            abort(403, 'Students cannot self-register for courses. Please contact the registrar.');
        }

        if (!$this->registrationOpen() && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Course registration is currently closed. Please contact the administrator.');
        }

        $request->validate([
            'student_id'           => 'required|exists:students,id',
            'course_offering_ids'  => 'required|array|min:1',
            'course_offering_ids.*'=> 'exists:course_offerings,id',
        ]);

        $student = Student::with('activeHolds')->findOrFail($request->student_id);

        // Check registration holds
        $blockingHold = $student->activeHolds->firstWhere('blocks_registration', true);
        if ($blockingHold) {
            return back()->with('error',
                "Registration blocked: {$blockingHold->type} hold — {$blockingHold->reason}"
            );
        }

        $registered = 0; $waitlisted = 0; $errors = [];

        foreach ($request->course_offering_ids as $offeringId) {
            $offering = CourseOffering::with('course.prerequisites.prerequisiteCourse')->find($offeringId);
            if (!$offering) { $errors[] = "Offering {$offeringId} not found."; continue; }

            // Already registered or waitlisted?
            if (CourseRegistration::where('student_id', $student->id)->where('course_offering_id', $offeringId)->exists()) {
                $errors[] = "{$offering->course->name}: Already registered."; continue;
            }
            if (CourseWaitlist::where('student_id', $student->id)->where('course_offering_id', $offeringId)->whereIn('status', ['waiting','notified'])->exists()) {
                $errors[] = "{$offering->course->name}: Already on waitlist."; continue;
            }

            // Prerequisite check
            $unmet = $this->checkPrerequisites($student, $offering->course);
            if (!empty($unmet)) {
                $errors[] = "{$offering->course->name}: Missing prerequisites — " . implode(', ', $unmet);
                continue;
            }

            // Full → waitlist
            if ($offering->is_full) {
                $position = CourseWaitlist::where('course_offering_id', $offeringId)->where('status', 'waiting')->max('position') + 1;
                CourseWaitlist::create([
                    'student_id'         => $student->id,
                    'course_offering_id' => $offeringId,
                    'position'           => $position,
                    'status'             => 'waiting',
                ]);
                $waitlisted++;
                continue;
            }

            CourseRegistration::create([
                'student_id'         => $student->id,
                'course_offering_id' => $offeringId,
                'registered_by'      => auth()->id(),
                'status'             => 'registered',
            ]);
            $offering->increment('enrolled_students');
            $registered++;
        }

        $parts = [];
        if ($registered)  $parts[] = "{$registered} course(s) registered";
        if ($waitlisted)  $parts[] = "{$waitlisted} added to waitlist (class full)";
        if ($errors)      $parts[] = 'Issues: ' . implode('; ', $errors);

        $level = $registered > 0 || $waitlisted > 0 ? 'success' : 'warning';
        return back()->with($level, implode('. ', $parts) . '.');
    }

    private function checkPrerequisites(Student $student, $course): array
    {
        $prerequisites = $course->prerequisites()->with('prerequisiteCourse')->get();
        if ($prerequisites->isEmpty()) return [];

        $unmet = [];
        foreach ($prerequisites as $prereq) {
            $passed = FinalResult::where('student_id', $student->id)
                ->whereHas('courseOffering', fn($q) => $q->where('course_id', $prereq->prerequisite_course_id))
                ->where('grade', '>=', $prereq->min_grade)
                ->exists();
            if (!$passed) {
                $unmet[] = $prereq->prerequisiteCourse->code . ' (min: ' . $prereq->min_grade . ')';
            }
        }
        return $unmet;
    }

    public function drop(CourseRegistration $registration)
    {
        $registration->update(['status' => 'dropped']);
        $offering = $registration->courseOffering;
        $offering->decrement('enrolled_students');

        // Promote next waitlisted student
        $next = CourseWaitlist::where('course_offering_id', $offering->id)
            ->where('status', 'waiting')
            ->orderBy('position')
            ->first();

        if ($next) {
            $next->update(['status' => 'notified', 'notified_at' => now()]);
            Notification::send(
                $next->student->user_id,
                'result',
                'Waitlist Spot Available',
                "A spot has opened in {$offering->course->name}. Log in to confirm your registration within 48 hours.",
                [],
                route('academic.registrations.my-courses')
            );
        }

        return back()->with('success', 'Course dropped successfully.' . ($next ? ' Next waitlisted student has been notified.' : ''));
    }

    public function confirmWaitlist(CourseWaitlist $waitlist)
    {
        if ($waitlist->student->user_id !== auth()->id()) abort(403);
        if ($waitlist->status !== 'notified') {
            return back()->with('error', 'This waitlist spot is no longer available.');
        }
        if (!$this->registrationOpen()) {
            return back()->with('error', 'Course registration is currently closed.');
        }

        $offering = $waitlist->courseOffering;
        if ($offering->is_full) {
            $waitlist->update(['status' => 'cancelled']);
            return back()->with('error', 'The spot was taken. You have been removed from the waitlist.');
        }

        CourseRegistration::create([
            'student_id'         => $waitlist->student_id,
            'course_offering_id' => $waitlist->course_offering_id,
            'registered_by'      => auth()->id(),
            'status'             => 'registered',
        ]);
        $offering->increment('enrolled_students');
        $waitlist->update(['status' => 'enrolled']);

        return redirect()->route('academic.registrations.my-courses')
            ->with('success', "Successfully enrolled in {$offering->course->name}.");
    }

    public function updateStatus(CourseRegistration $registration, Request $request)
    {
        $request->validate(['status' => 'required|in:registered,dropped,completed,failed,approved']);
        $old = $registration->status;
        $registration->update(['status' => $request->status]);

        if ($old !== 'dropped' && $request->status === 'dropped') {
            $registration->courseOffering?->decrement('enrolled_students');
        } elseif ($old === 'dropped' && $request->status === 'registered') {
            $registration->courseOffering?->increment('enrolled_students');
        }

        return response()->json(['success' => true, 'status' => $registration->status]);
    }

    public function studentSubjects(Student $student)
    {
        $regs = CourseRegistration::where('student_id', $student->id)
            ->with(['courseOffering.course', 'courseOffering.semester', 'courseOffering.lecturer.user'])
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'course_code' => optional(optional($r->courseOffering)->course)->code ?? '—',
                'course_name' => optional(optional($r->courseOffering)->course)->name
                              ?? optional(optional($r->courseOffering)->course)->title ?? '—',
                'semester'    => optional(optional($r->courseOffering)->semester)->name ?? '—',
                'lecturer'    => optional(optional(optional($r->courseOffering)->lecturer)->user)->name ?? '—',
                'status'      => $r->status,
                'registered_on' => $r->created_at?->format('d M Y') ?? '—',
            ]);

        return response()->json($regs);
    }

    public function myCourses()
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();
        $semester = Semester::where('is_current', true)->first();
        $registrations = CourseRegistration::where('student_id', $student->id)
            ->whereHas('courseOffering', fn($q) => $q->where('semester_id', $semester?->id))
            ->with(['courseOffering.course', 'courseOffering.lecturer', 'courseOffering.timetables'])
            ->get();
        $waitlistEntries = \App\Models\CourseWaitlist::where('student_id', $student->id)
            ->whereIn('status', ['waiting', 'notified'])
            ->with('courseOffering.course')
            ->orderBy('position')
            ->get();
        $registrationOpen = $this->registrationOpen();
        return view('academic.registrations.my-courses', compact('student', 'semester', 'registrations', 'waitlistEntries', 'registrationOpen'));
    }
}
