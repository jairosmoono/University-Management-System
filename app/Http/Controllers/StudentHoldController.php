<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Student;
use App\Models\StudentHold;
use Illuminate\Http\Request;

class StudentHoldController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentHold::with(['student.user', 'placedBy', 'releasedBy']);

        if ($request->type)      $query->where('type', $request->type);
        if ($request->is_active !== null) $query->where('is_active', $request->boolean('is_active'));
        if ($request->search) {
            $query->whereHas('student', fn($q) => $q
                ->where('student_id', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
            );
        }

        $holds = $query->latest()->paginate(25);

        $stats = [
            'active'    => StudentHold::where('is_active', true)->count(),
            'released'  => StudentHold::where('is_active', false)->count(),
            'blocking'  => StudentHold::where('is_active', true)->where('blocks_registration', true)->count(),
            'by_type'   => StudentHold::where('is_active', true)->selectRaw('type, count(*) as total')
                               ->groupBy('type')->pluck('total', 'type'),
        ];

        $types = ['financial', 'academic', 'disciplinary', 'library', 'hostel', 'administrative'];

        return view('student-holds.index', compact('holds', 'stats', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'          => 'required|exists:students,id',
            'type'                => 'required|in:financial,academic,disciplinary,library,hostel,administrative',
            'reason'              => 'required|string|max:500',
            'blocks_registration' => 'boolean',
        ]);

        $hold = StudentHold::create([
            'student_id'          => $request->student_id,
            'type'                => $request->type,
            'reason'              => $request->reason,
            'blocks_registration' => $request->boolean('blocks_registration', true),
            'placed_by'           => auth()->id(),
            'is_active'           => true,
        ]);

        $student = Student::find($request->student_id);
        Notification::send(
            $student->user_id,
            'academic',
            'Account Hold Placed',
            StudentHold::typeLabel($request->type) . ' hold placed on your account: ' . $request->reason,
            [],
            route('academic.student-holds.index')
        );

        return back()->with('success', 'Hold placed on student account.');
    }

    public function release(StudentHold $studentHold)
    {
        $studentHold->update([
            'is_active'   => false,
            'released_by' => auth()->id(),
            'released_at' => now(),
        ]);

        Notification::send(
            $studentHold->student->user_id,
            'academic',
            'Account Hold Released',
            StudentHold::typeLabel($studentHold->type) . ' hold on your account has been released.',
        );

        return back()->with('success', 'Hold released successfully.');
    }

    public function studentHolds(Student $student)
    {
        $holds = $student->holds()->with(['placedBy', 'releasedBy'])->latest()->get();
        $types = ['financial', 'academic', 'disciplinary', 'library', 'hostel', 'administrative'];
        return view('student-holds.student', compact('student', 'holds', 'types'));
    }
}
