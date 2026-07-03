<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Assignment::with(['courseOffering.course', 'courseOffering.semester'])
            ->withCount('submissions');

        if ($user->hasRole('lecturer')) {
            $staff = Staff::where('user_id', $user->id)->first();
            $query->whereHas('courseOffering', fn($q) => $q->where('lecturer_id', $staff?->id));
        }

        if ($request->status)     $query->where('status', $request->status);
        if ($request->offering_id) $query->where('course_offering_id', $request->offering_id);
        if ($request->program_id && !$user->hasRole('lecturer')) {
            $query->whereHas('courseOffering.course', fn($q) =>
                $q->whereHas('department', fn($d) =>
                    $d->whereHas('programs', fn($p) => $p->where('programs.id', $request->program_id))
                )
            );
        }

        $assignments = $query->latest()->paginate(20);

        $offerings = $user->hasRole('lecturer')
            ? CourseOffering::where('lecturer_id', Staff::where('user_id', $user->id)->value('id'))->with('course')->get()
            : CourseOffering::with('course')->get();

        $programs = $user->hasRole('lecturer') ? collect() : Program::orderBy('name')->get();

        return view('academic.assignments.index', compact('assignments', 'offerings', 'programs'));
    }

    public function create()
    {
        $user  = auth()->user();
        $staff = Staff::where('user_id', $user->id)->first();

        $offerings = $user->hasRole('lecturer')
            ? CourseOffering::where('lecturer_id', $staff?->id)->with('course', 'semester')->get()
            : CourseOffering::with('course', 'semester')->get();

        return view('academic.assignments.create', compact('offerings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'due_date'           => 'required|date',
            'total_marks'        => 'required|numeric|min:1|max:100',
        ]);

        Assignment::create([
            'course_offering_id' => $request->course_offering_id,
            'title'              => $request->title,
            'description'        => $request->description,
            'due_date'           => $request->due_date,
            'total_marks'        => $request->total_marks,
            'status'             => 'draft',
        ]);

        return redirect()->route('academic.assignments.index')
            ->with('success', 'Assignment created. Publish it when ready.');
    }

    public function show(Assignment $assignment)
    {
        $assignment->load(['courseOffering.course', 'courseOffering.semester',
            'submissions.student.user']);

        $submissions = $assignment->submissions()->with('student.user')->latest()->get();

        // Count enrolled students who haven't submitted
        $enrolledCount = $assignment->courseOffering->approvedRegistrations()->count();
        $submittedIds  = $submissions->pluck('student_id');

        return view('academic.assignments.show', compact('assignment', 'submissions', 'enrolledCount', 'submittedIds'));
    }

    public function edit(Assignment $assignment)
    {
        $user  = auth()->user();
        $staff = Staff::where('user_id', $user->id)->first();

        $offerings = $user->hasRole('lecturer')
            ? CourseOffering::where('lecturer_id', $staff?->id)->with('course', 'semester')->get()
            : CourseOffering::with('course', 'semester')->get();

        return view('academic.assignments.edit', compact('assignment', 'offerings'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date'    => 'required|date',
            'total_marks' => 'required|numeric|min:1|max:100',
        ]);

        $assignment->update($request->only('title', 'description', 'due_date', 'total_marks'));

        return redirect()->route('academic.assignments.show', $assignment)
            ->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment)
    {
        // Delete any uploaded submission files
        foreach ($assignment->submissions as $sub) {
            if ($sub->file_path) Storage::disk('public')->delete($sub->file_path);
        }
        $assignment->delete();
        return redirect()->route('academic.assignments.index')
            ->with('success', 'Assignment deleted.');
    }

    public function publish(Assignment $assignment)
    {
        $assignment->update(['status' => 'published']);
        return back()->with('success', 'Assignment published. Students can now see and submit it.');
    }

    public function close(Assignment $assignment)
    {
        $assignment->update(['status' => 'closed']);
        return back()->with('success', 'Assignment closed. No more submissions accepted.');
    }

    public function performanceSheet(Assignment $assignment)
    {
        $assignment->load([
            'courseOffering.course.department',
            'courseOffering.semester.academicYear',
            'courseOffering.lecturer.user',
            'submissions.student.user',
        ]);

        // All enrolled students for this offering, ordered by student_id
        $enrolledStudents = \App\Models\CourseRegistration::where('course_offering_id', $assignment->course_offering_id)
            ->whereIn('status', ['registered', 'completed'])
            ->with('student.user')
            ->get()
            ->map->student
            ->filter()
            ->sortBy('student_id')
            ->values();

        // Keyed by student_id for O(1) lookup
        $submissionsMap = $assignment->submissions->keyBy('student_id');

        return view('academic.assignments.performance-sheet', compact(
            'assignment', 'enrolledStudents', 'submissionsMap'
        ));
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $request->validate([
            'marks_obtained' => 'required|numeric|min:0|max:' . $assignment->total_marks,
            'feedback'       => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'marks_obtained' => $request->marks_obtained,
            'feedback'       => $request->feedback,
            'graded_by'      => auth()->id(),
            'graded_at'      => now(),
            'status'         => 'graded',
        ]);

        return back()->with('success', "Submission graded: {$request->marks_obtained}/{$assignment->total_marks}.");
    }

    // ── Student-facing ────────────────────────────────────────────────────────

    public function myAssignments()
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $registeredOfferingIds = $student->courseRegistrations()
            ->where('status', 'registered')
            ->pluck('course_offering_id');

        $assignments = Assignment::where('status', 'published')
            ->whereIn('course_offering_id', $registeredOfferingIds)
            ->with('courseOffering.course', 'courseOffering.semester')
            ->withCount('submissions')
            ->latest('due_date')
            ->get();

        // Map assignment_id → my submission
        $mySubmissions = AssignmentSubmission::where('student_id', $student->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->get()
            ->keyBy('assignment_id');

        return view('academic.assignments.my-assignments', compact('assignments', 'mySubmissions', 'student'));
    }

    public function submitForm(Assignment $assignment)
    {
        if ($assignment->status !== 'published') abort(403, 'This assignment is not open for submissions.');

        $student    = Student::where('user_id', auth()->id())->firstOrFail();
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)->first();

        return view('academic.assignments.submit', compact('assignment', 'submission', 'student'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        if ($assignment->status !== 'published') abort(403);

        $request->validate([
            'submission_text' => 'nullable|string',
            'file'            => 'nullable|file|max:10240|mimes:pdf,doc,docx,txt,zip,jpg,jpeg,png',
        ]);

        if (!$request->filled('submission_text') && !$request->hasFile('file')) {
            return back()->with('error', 'Please provide a text response or upload a file.');
        }

        $student  = Student::where('user_id', auth()->id())->firstOrFail();
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("assignments/{$assignment->id}", 'public');
        }

        $isLate   = $assignment->due_date && now()->gt($assignment->due_date);
        $status   = $isLate ? 'late' : 'submitted';

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => $student->id],
            [
                'submission_text' => $request->submission_text,
                'file_path'       => $filePath ?? AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_id', $student->id)->value('file_path'),
                'submitted_at'    => now(),
                'status'          => $status,
                'marks_obtained'  => null,
                'feedback'        => null,
                'graded_by'       => null,
                'graded_at'       => null,
            ]
        );

        return redirect()->route('academic.assignments.my')
            ->with('success', 'Assignment submitted successfully.' . ($isLate ? ' (Late submission)' : ''));
    }
}
