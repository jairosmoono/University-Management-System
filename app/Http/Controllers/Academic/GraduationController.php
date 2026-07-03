<?php
namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\GraduationApplication;
use App\Models\GraduationCeremony;
use App\Models\Student;
use App\Models\Alumni;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\StudentBill;
use App\Models\BookBorrowing;
use App\Models\FinalResult;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GraduationController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('student')) {
            return $this->studentPortal();
        }

        $query = GraduationApplication::with(['student.user', 'program', 'academicYear', 'ceremony']);

        if ($request->status)           $query->where('status', $request->status);
        if ($request->program_id)       $query->where('program_id', $request->program_id);
        if ($request->academic_year_id) $query->where('academic_year_id', $request->academic_year_id);
        if ($request->ceremony_id)      $query->where('ceremony_id', $request->ceremony_id);

        $applications = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'     => GraduationApplication::count(),
            'pending'   => GraduationApplication::whereIn('status', ['pending', 'under_review'])->count(),
            'cleared'   => GraduationApplication::where('status', 'cleared')->count(),
            'approved'  => GraduationApplication::where('status', 'approved')->count(),
            'graduated' => GraduationApplication::where('status', 'graduated')->count(),
            'rejected'  => GraduationApplication::where('status', 'rejected')->count(),
        ];

        $programs      = Program::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('name')->get();
        $ceremonies    = GraduationCeremony::orderByDesc('ceremony_date')->get();

        return view('academic.graduations.index', compact(
            'applications', 'stats', 'programs', 'academicYears', 'ceremonies'
        ));
    }

    private function studentPortal()
    {
        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }
        $application = GraduationApplication::where('student_id', $student->id)
            ->with(['program', 'academicYear', 'ceremony'])
            ->latest()->first();
        $eligibility = $this->buildEligibilityData($student);

        return view('academic.graduations.student', compact('student', 'application', 'eligibility'));
    }

    public function eligible(Request $request)
    {
        $appliedIds = GraduationApplication::pluck('student_id');

        $query = Student::with(['user', 'program.department'])
            ->where('status', 'graduated')
            ->whereNotIn('id', $appliedIds);

        if ($request->program_id) $query->where('program_id', $request->program_id);
        if ($request->search) {
            $q = $request->search;
            $query->where(function ($qb) use ($q) {
                $qb->where('student_id', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->ajax() || $request->has('_ajax')) {
            $results = $query->limit(15)->get()->map(fn($s) => [
                'id'         => $s->id,
                'student_id' => $s->student_id,
                'name'       => $s->full_name,
                'program'    => $s->program?->name ?? '—',
            ]);
            return response()->json($results);
        }

        $students = $query->get()->map(fn($s) => [
            'student'     => $s,
            'eligibility' => $this->buildEligibilityData($s),
        ])->sortByDesc(fn($row) => $row['eligibility']['eligible']);

        $programs = Program::active()->orderBy('name')->get();

        return view('academic.graduations.eligible', compact('students', 'programs'));
    }

    public function apply(Request $request)
    {
        $student     = $request->student_id ? Student::with(['user', 'program'])->findOrFail($request->student_id) : null;
        $eligibility = $student ? $this->buildEligibilityData($student) : null;

        $academicYears = AcademicYear::orderByDesc('name')->get();
        $ceremonies    = GraduationCeremony::whereIn('status', ['planned', 'confirmed'])->orderBy('ceremony_date')->get();

        return view('academic.graduations.apply', compact('student', 'eligibility', 'academicYears', 'ceremonies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'       => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'ceremony_id'      => 'nullable|exists:graduation_ceremonies,id',
            'notes'            => 'nullable|string|max:1000',
        ]);

        if (GraduationApplication::where('student_id', $validated['student_id'])->exists()) {
            return back()->with('error', 'This student already has a graduation application.');
        }

        $student    = Student::findOrFail($validated['student_id']);
        $latestGpa  = $student->gpaRecords()->orderByDesc('id')->first();

        GraduationApplication::create([
            'student_id'       => $validated['student_id'],
            'program_id'       => $student->program_id,
            'academic_year_id' => $validated['academic_year_id'],
            'ceremony_id'      => $validated['ceremony_id'] ?? null,
            'cgpa'             => $latestGpa?->cgpa ?? 0,
            'credits_earned'   => $latestGpa?->total_credits_earned ?? 0,
            'status'           => 'pending',
            'notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('graduation.index')->with('success', 'Graduation application submitted successfully.');
    }

    public function show(GraduationApplication $application)
    {
        $application->load(['student.user', 'student.program', 'program.department', 'academicYear', 'ceremony', 'approvedBy']);
        $eligibility = $this->buildEligibilityData($application->student);
        $ceremonies  = GraduationCeremony::whereIn('status', ['planned', 'confirmed'])->orderBy('ceremony_date')->get();

        return view('academic.graduations.show', compact('application', 'eligibility', 'ceremonies'));
    }

    public function updateClearance(Request $request, GraduationApplication $application)
    {
        $validated = $request->validate([
            'finance_cleared'  => 'nullable|boolean',
            'library_cleared'  => 'nullable|boolean',
            'academic_cleared' => 'nullable|boolean',
            'ceremony_id'      => 'nullable|exists:graduation_ceremonies,id',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $application->update($validated);
        $application->refresh();

        if ($application->isFullyCleared() && in_array($application->status, ['pending', 'under_review'])) {
            $application->update(['status' => 'cleared', 'cleared_at' => now()]);
        }

        return back()->with('success', 'Clearance updated successfully.');
    }

    public function review(GraduationApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be moved to under review.');
        }
        $application->update(['status' => 'under_review']);
        return back()->with('success', 'Application is now under review.');
    }

    public function approve(Request $request, GraduationApplication $application)
    {
        $request->validate([
            'graduation_date' => 'nullable|date',
            'ceremony_id'     => 'nullable|exists:graduation_ceremonies,id',
        ]);

        if (!in_array($application->status, ['cleared', 'under_review', 'pending'])) {
            return back()->with('error', 'Cannot approve at this stage.');
        }

        $application->update([
            'status'          => 'approved',
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
            'graduation_date' => $request->graduation_date,
            'ceremony_id'     => $request->ceremony_id ?? $application->ceremony_id,
        ]);

        return back()->with('success', 'Graduation application approved.');
    }

    public function reject(Request $request, GraduationApplication $application)
    {
        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        if ($application->status === 'graduated') {
            return back()->with('error', 'Cannot reject a completed graduation.');
        }

        $application->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Application rejected.');
    }

    public function markGraduated(Request $request, GraduationApplication $application)
    {
        if ($application->status !== 'approved') {
            return back()->with('error', 'Application must be approved before marking as graduated.');
        }

        $graduationDate = $request->graduation_date ?? $application->graduation_date ?? today()->toDateString();

        $application->update([
            'status'          => 'graduated',
            'graduation_date' => $graduationDate,
        ]);

        $application->student->update(['status' => 'graduated']);

        Alumni::updateOrCreate(
            ['student_id' => $application->student_id],
            ['graduation_year' => \Carbon\Carbon::parse($graduationDate)->year]
        );

        return back()->with('success', 'Student marked as graduated and alumni record created.');
    }

    public function certificate(GraduationApplication $application)
    {
        if (!in_array($application->status, ['approved', 'graduated'])) {
            abort(403, 'Certificate not available for this application.');
        }

        $application->load(['student.user', 'program.department.faculty', 'academicYear', 'approvedBy']);

        $pdf = Pdf::loadView('academic.graduations.certificate', compact('application'))
            ->setPaper('a4', 'landscape');

        $slug = str_replace(' ', '_', $application->student->full_name);
        return $pdf->download("graduation_certificate_{$slug}.pdf");
    }

    public function certificatePreview(GraduationApplication $application)
    {
        abort_if(auth()->user()->hasRole('student'), 403);

        $application->load(['student.user', 'program.department.faculty', 'academicYear', 'approvedBy']);

        return view('academic.graduations.certificate_preview', compact('application'));
    }

    public function certificateSamplePreview()
    {
        abort_if(auth()->user()->hasRole('student'), 403);

        // Build a plain object mirroring the shape the view expects
        $application = (object) [
            'id'              => 'SAMPLE',
            'cgpa'            => 3.75,
            'credits_earned'  => 120,
            'status'          => 'approved',
            'graduation_date' => now(),
            'notes'           => null,
            'ceremony'        => null,
            'approved_at'     => null,
            'approvedBy'      => null,
            'student'         => (object) [
                'full_name'  => 'Jane Wanjiru Mwangi',
                'student_id' => 'STU-2024-0001',
                'photo_url'  => null,
            ],
            'program'         => (object) [
                'name'        => 'Bachelor of Science in Computer Science',
                'level_label' => 'Undergraduate',
                'department'  => (object) [
                    'name'    => 'Department of Computing',
                    'faculty' => (object) ['name' => 'Faculty of Science & Technology'],
                ],
            ],
            'academicYear'    => (object) ['name' => date('Y').'/'.((int) date('Y') + 1)],
        ];

        return view('academic.graduations.certificate_preview', compact('application'));
    }

    public function ceremonies(Request $request)
    {
        $query = GraduationCeremony::with('academicYear')
            ->withCount(['applications as graduates_count' => fn($q) => $q->whereIn('status', ['approved', 'graduated'])]);

        if ($request->status) $query->where('status', $request->status);
        if ($request->academic_year_id) $query->where('academic_year_id', $request->academic_year_id);

        $ceremonies    = $query->orderByDesc('ceremony_date')->paginate(15)->withQueryString();
        $academicYears = AcademicYear::orderByDesc('name')->get();

        return view('academic.graduations.ceremonies.index', compact('ceremonies', 'academicYears'));
    }

    public function createCeremony()
    {
        $academicYears = AcademicYear::orderByDesc('name')->get();
        return view('academic.graduations.ceremonies.create', compact('academicYears'));
    }

    public function storeCeremony(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'ceremony_date'    => 'required|date',
            'venue'            => 'nullable|string|max:255',
            'dress_code'       => 'nullable|string|max:255',
            'max_graduates'    => 'nullable|integer|min:1',
            'status'           => 'required|in:planned,confirmed,completed,cancelled',
            'notes'            => 'nullable|string',
        ]);

        $ceremony = GraduationCeremony::create([...$validated, 'created_by' => auth()->id()]);

        return redirect()->route('graduation.ceremonies.show', $ceremony)
            ->with('success', 'Graduation ceremony created successfully.');
    }

    public function showCeremony(GraduationCeremony $ceremony)
    {
        $ceremony->load(['academicYear', 'createdBy']);
        $graduates = $ceremony->applications()
            ->with(['student.user', 'program'])
            ->whereIn('status', ['approved', 'graduated'])
            ->orderBy('status')
            ->get();

        return view('academic.graduations.ceremonies.show', compact('ceremony', 'graduates'));
    }

    public function destroy(GraduationApplication $application)
    {
        if ($application->status === 'graduated') {
            return back()->with('error', 'Cannot delete a completed graduation record.');
        }
        $application->delete();
        return redirect()->route('graduation.index')->with('success', 'Application deleted.');
    }

    public function destroyCeremony(GraduationCeremony $ceremony)
    {
        if ($ceremony->applications()->whereIn('status', ['approved', 'graduated'])->exists()) {
            return back()->with('error', 'Cannot delete a ceremony with approved or graduated students.');
        }

        $ceremony->applications()->update(['ceremony_id' => null]);
        $ceremony->delete();

        return redirect()->route('graduation.ceremonies.index')->with('success', 'Ceremony deleted successfully.');
    }

    private function buildEligibilityData(Student $student): array
    {
        $latestGpa     = $student->gpaRecords()->orderByDesc('id')->first();
        $creditsEarned = (int) ($latestGpa?->total_credits_earned ?? 0);
        $cgpa          = (float) ($latestGpa?->cgpa ?? 0);
        $program       = $student->program;
        $requiredCredits = (int) ($program?->credit_hours_required ?? 0);

        $creditsOk = $requiredCredits > 0 && $creditsEarned >= $requiredCredits;
        $cgpaOk    = $cgpa >= 1.5;

        $outstandingBal = (float) StudentBill::where('student_id', $student->id)->sum('balance');
        $financeOk      = $outstandingBal <= 0;

        $activeLoans = BookBorrowing::where('student_id', $student->id)->where('status', 'borrowed')->count();
        $unpaidFines = (float) BookBorrowing::where('student_id', $student->id)
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->where('fine_waived', false)
            ->sum('fine_amount');
        $libraryOk = $activeLoans === 0 && $unpaidFines <= 0;

        $failedCount     = FinalResult::where('student_id', $student->id)
            ->where('total_score', '<', 40)->where('status', 'published')->count();
        $pendingResults  = FinalResult::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])->count();
        $academicOk      = $failedCount === 0 && $pendingResults === 0;

        return [
            'credits_earned'   => $creditsEarned,
            'required_credits' => $requiredCredits,
            'credits_ok'       => $creditsOk,
            'cgpa'             => round($cgpa, 2),
            'cgpa_ok'          => $cgpaOk,
            'outstanding_bal'  => round($outstandingBal, 2),
            'finance_ok'       => $financeOk,
            'active_loans'     => $activeLoans,
            'unpaid_fines'     => round($unpaidFines, 2),
            'library_ok'       => $libraryOk,
            'failed_count'     => $failedCount,
            'pending_results'  => $pendingResults,
            'academic_ok'      => $academicOk,
            'eligible'         => $creditsOk && $cgpaOk && $financeOk && $libraryOk && $academicOk,
        ];
    }
}
