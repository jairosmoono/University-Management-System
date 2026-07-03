<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\FinalResult;
use App\Models\GradeAppeal;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GradeAppealController extends Controller
{
    public function index(Request $request)
    {
        $user      = auth()->user();
        $isStudent = $user->hasRole('student');

        $query = GradeAppeal::with([
            'student.user', 'courseOffering.course',
            'courseOffering.semester', 'reviewedBy',
        ]);

        if ($isStudent) {
            $student = Student::where('user_id', $user->id)->firstOrFail();
            $query->where('student_id', $student->id);
        }

        if ($request->status) $query->where('status', $request->status);

        $appeals = $query->latest()->paginate(20);

        $stats = [
            'pending'      => GradeAppeal::where('status', 'pending')->count(),
            'under_review' => GradeAppeal::where('status', 'under_review')->count(),
            'approved'     => GradeAppeal::where('status', 'approved')->count(),
            'rejected'     => GradeAppeal::where('status', 'rejected')->count(),
        ];

        return view('grade-appeals.index', compact('appeals', 'stats', 'isStudent'));
    }

    public function create()
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $results = FinalResult::where('student_id', $student->id)
            ->with(['courseOffering.course', 'courseOffering.semester'])
            ->whereDoesntHave('gradeAppeals', fn($q) => $q->whereIn('status', ['pending', 'under_review']))
            ->latest()
            ->get();

        return view('grade-appeals.create', compact('student', 'results'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'final_result_id'    => 'required|exists:final_results,id',
            'reason'             => 'required|string|min:30|max:2000',
            'supporting_document'=> 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $student = Student::where('user_id', auth()->id())->firstOrFail();
        $result  = FinalResult::with('courseOffering')->findOrFail($request->final_result_id);

        if ($result->student_id !== $student->id) abort(403);

        $docPath = null;
        if ($request->hasFile('supporting_document')) {
            $docPath = $request->file('supporting_document')->store('grade-appeals', 'public');
        }

        GradeAppeal::create([
            'student_id'          => $student->id,
            'course_offering_id'  => $result->course_offering_id,
            'final_result_id'     => $result->id,
            'reason'              => $request->reason,
            'supporting_document' => $docPath,
            'status'              => 'pending',
            'original_grade'      => $result->grade,
            'original_total'      => $result->total_score,
        ]);

        return redirect()->route('academic.grade-appeals.index')
            ->with('success', 'Grade appeal submitted. You will be notified of the outcome.');
    }

    public function show(GradeAppeal $gradeAppeal)
    {
        $this->authorizeAppeal($gradeAppeal);
        $gradeAppeal->load(['student.user', 'courseOffering.course', 'courseOffering.semester', 'finalResult', 'reviewedBy']);
        return view('grade-appeals.show', compact('gradeAppeal'));
    }

    public function review(Request $request, GradeAppeal $gradeAppeal)
    {
        $request->validate([
            'status'        => 'required|in:under_review,approved,rejected',
            'admin_notes'   => 'required|string|max:2000',
            'revised_grade' => 'nullable|required_if:status,approved|string|max:5',
            'revised_total' => 'nullable|required_if:status,approved|numeric|min:0|max:100',
        ]);

        $gradeAppeal->update([
            'status'        => $request->status,
            'admin_notes'   => $request->admin_notes,
            'revised_grade' => $request->revised_grade,
            'revised_total' => $request->revised_total,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        // If approved, update the final result
        if ($request->status === 'approved' && $gradeAppeal->final_result_id) {
            $gradeAppeal->finalResult->update([
                'grade'       => $request->revised_grade,
                'total_score' => $request->revised_total,
            ]);
        }

        // Notify student
        $statusLabel = match($request->status) {
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'under_review' => 'Under Review',
        };
        Notification::send(
            $gradeAppeal->student->user_id,
            'result',
            "Grade Appeal {$statusLabel}",
            "Your grade appeal for {$gradeAppeal->courseOffering->course->name} has been {$statusLabel}.",
            [],
            route('academic.grade-appeals.show', $gradeAppeal)
        );

        return back()->with('success', "Appeal marked as {$statusLabel}.");
    }

    private function authorizeAppeal(GradeAppeal $appeal): void
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            if ($appeal->student_id !== $student?->id) abort(403);
        }
    }
}
