<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentBill;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    public function index(Request $request)
    {
        $academicYears   = AcademicYear::orderByDesc('start_date')->get();
        $semesters       = Semester::with('academicYear')->orderByDesc('created_at')->get();
        $programs        = Program::orderBy('name')->get();
        $currentYear     = AcademicYear::current();
        $currentSemester = Semester::current();

        // ── Summary cards ──────────────────────────────────────────────
        $totalCollected  = Payment::where('status', 'verified')->sum('amount');
        $totalBilled     = StudentBill::sum('total_amount');
        $totalOutstanding = StudentBill::where('status', '!=', 'paid')->sum('balance');
        $totalBills      = StudentBill::count();
        $collectionRate  = $totalBilled > 0
            ? round(($totalCollected / $totalBilled) * 100, 1)
            : 0;

        $summary = [
            'total_collected'  => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'total_bills'      => $totalBills,
            'collection_rate'  => $collectionRate,
        ];

        // ── Monthly trend chart (last 12 months) ───────────────────────
        $monthlyTrend = Payment::where('status', 'verified')
            ->where('payment_date', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(payment_date, '%b %Y') as month,
                         DATE_FORMAT(payment_date, '%Y-%m') as sort_key,
                         SUM(amount) as amount")
            ->groupBy('month', 'sort_key')
            ->orderBy('sort_key')
            ->get(['month', 'amount']);

        // ── Revenue by programme chart ──────────────────────────────────
        $programRevenue = Payment::where('payments.status', 'verified')
            ->join('student_bills', 'payments.student_bill_id', '=', 'student_bills.id')
            ->join('students',      'student_bills.student_id', '=', 'students.id')
            ->join('programs',      'students.program_id',      '=', 'programs.id')
            ->selectRaw('programs.code, programs.name, SUM(payments.amount) as total')
            ->groupBy('programs.id', 'programs.code', 'programs.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get(['programs.code', 'programs.name', 'total']);

        return view('finance.reports.index', compact(
            'academicYears', 'semesters', 'programs',
            'currentYear', 'currentSemester',
            'summary', 'monthlyTrend', 'programRevenue'
        ));
    }

    public function collectionReport(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $semesters     = Semester::with('academicYear')->orderByDesc('created_at')->get();

        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to   = $request->to_date   ?? now()->toDateString();

        // Base filter closure (reused for both aggregate and paginated queries)
        $applyFilters = function ($q) use ($request, $from, $to) {
            $q->where('status', 'verified')->whereBetween('payment_date', [$from, $to]);
            if ($request->academic_year_id) {
                $q->whereHas('studentBill', fn($sq) => $sq->where('academic_year_id', $request->academic_year_id));
            }
            if ($request->semester_id) {
                $q->whereHas('studentBill', fn($sq) => $sq->where('semester_id', $request->semester_id));
            }
        };

        // Aggregates (no eager loading needed)
        $aggQuery = Payment::query();
        $applyFilters($aggQuery);
        $totalAmount    = $aggQuery->sum('amount');
        $totalCount     = $aggQuery->count();
        $uniqueStudents = Payment::query()
            ->join('student_bills', 'payments.student_bill_id', '=', 'student_bills.id')
            ->where('payments.status', 'verified')
            ->whereBetween('payments.payment_date', [$from, $to])
            ->when($request->academic_year_id, fn($q) => $q->where('student_bills.academic_year_id', $request->academic_year_id))
            ->when($request->semester_id, fn($q) => $q->where('student_bills.semester_id', $request->semester_id))
            ->distinct('student_bills.student_id')
            ->count('student_bills.student_id');

        $totals = [
            'amount'   => $totalAmount,
            'count'    => $totalCount,
            'average'  => $totalCount ? $totalAmount / $totalCount : 0,
            'students' => $uniqueStudents,
        ];

        // By-method breakdown
        $byMethodRaw = (clone $aggQuery)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();
        $byMethod = $byMethodRaw->map(fn($r) => (object)[
            'payment_method' => $r->payment_method,
            'count'          => $r->count,
            'total'          => $r->total,
        ]);

        // Paginated display
        $pagedQuery = Payment::with(['studentBill.student.user', 'studentBill.student.program', 'recordedBy']);
        $applyFilters($pagedQuery);

        if ($request->export === 'pdf') {
            $allPayments = $pagedQuery->orderByDesc('payment_date')->get();
            $pdf = Pdf::loadView('finance.reports.collection-pdf', [
                'payments' => $allPayments, 'byMethod' => $byMethod,
                'from' => $from, 'to' => $to, 'totals' => $totals,
            ])->setPaper('a4', 'landscape');
            return $pdf->download("collection_report_{$from}_to_{$to}.pdf");
        }

        $payments = $pagedQuery->orderByDesc('payment_date')->paginate(50)->withQueryString();
        return view('finance.reports.collection', compact('payments', 'byMethod', 'from', 'to', 'academicYears', 'semesters', 'totals'));
    }

    public function outstandingReport(Request $request)
    {
        $academicYears  = AcademicYear::orderByDesc('start_date')->get();
        $semesters      = Semester::with('academicYear')->orderByDesc('created_at')->get();
        $programs       = Program::orderBy('name')->get();
        $academicYearId = $request->academic_year_id ?? optional(AcademicYear::current())->id;
        $semesterId     = $request->semester_id;

        $billQuery = StudentBill::with(['student.program.department.faculty', 'academicYear', 'semester'])
            ->where('status', '!=', 'paid')
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
            ->when($request->program_id, fn($q) => $q->whereHas('student', fn($sq) => $sq->where('program_id', $request->program_id)))
            ->when($request->status, fn($q) => $q->where('status', $request->status));

        // Summary stats — all derived from the same filtered query
        $totals = [
            'total_outstanding' => (clone $billQuery)->sum('balance'),
            'unpaid_count'      => (clone $billQuery)->where('status', 'unpaid')->count(),
            'partial_count'     => (clone $billQuery)->where('status', 'partial')->count(),
        ];

        $byProgram = (clone $billQuery)->get()
            ->groupBy(fn($b) => optional($b->student?->program)->name ?? 'Unknown')
            ->map(fn($g) => ['count' => $g->count(), 'total' => $g->sum('balance')]);

        if ($request->export === 'pdf') {
            $allBills = (clone $billQuery)->orderByDesc('balance')->get();
            $pdf = Pdf::loadView('finance.reports.outstanding-pdf', [
                'bills' => $allBills, 'totals' => $totals, 'byProgram' => $byProgram,
            ])->setPaper('a4', 'landscape');
            return $pdf->download('outstanding_bills_report.pdf');
        }

        $bills = $billQuery->orderByDesc('balance')->paginate(50)->withQueryString();
        return view('finance.reports.outstanding', compact('bills', 'totals', 'byProgram', 'academicYearId', 'semesterId', 'academicYears', 'semesters', 'programs'));
    }
}
