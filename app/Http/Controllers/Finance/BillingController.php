<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\StudentBill;
use App\Models\BillItem;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('student')) {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            return $this->studentBill($student);
        }

        $query = StudentBill::with(['student', 'academicYear', 'semester']);
        if ($request->student_id) $query->where('student_id', $request->student_id);
        if ($request->semester_id) $query->where('semester_id', $request->semester_id);
        if ($request->status) $query->where('status', $request->status);
        $bills = $query->latest()->paginate(20);
        $semesters      = Semester::orderBy('start_date', 'desc')->get();
        $academicYears  = AcademicYear::orderBy('name', 'desc')->get();
        $feeStructures  = FeeStructure::active()->orderBy('name')->get();
        $stats = [
            'total'        => StudentBill::count(),
            'total_amount' => StudentBill::sum('total_amount'),
            'amount_paid'  => StudentBill::sum('amount_paid'),
            'outstanding'  => StudentBill::sum('balance'),
        ];
        return view('finance.billing.index', compact('bills', 'semesters', 'academicYears', 'feeStructures', 'stats'));
    }

    public function show(StudentBill $bill)
    {
        $bill->load(['student.user', 'student.program', 'feeStructure', 'academicYear', 'semester', 'items', 'payments']);
        return view('finance.billing.show', compact('bill'));
    }

    public function studentBill(Student $student)
    {
        $bills = StudentBill::where('student_id', $student->id)
            ->with(['academicYear', 'semester', 'items', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_billed'  => $bills->sum('total_amount'),
            'total_paid'    => $bills->sum('amount_paid'),
            'outstanding'   => $bills->sum('balance'),
            'unpaid_count'  => $bills->whereIn('status', ['unpaid', 'partial'])->count(),
        ];

        $isSelfView = auth()->user()->hasRole('student');

        return view('finance.billing.student', compact('student', 'bills', 'stats', 'isSelfView'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'student_ids' => 'sometimes|array',
        ]);

        $semester = Semester::with('academicYear')->findOrFail($request->semester_id);
        $studentsQuery = Student::active();
        if ($request->student_ids) $studentsQuery->whereIn('id', $request->student_ids);
        $students = $studentsQuery->get();

        $generated = 0;
        DB::transaction(function () use ($students, $semester, &$generated) {
            foreach ($students as $student) {
                $existing = StudentBill::where('student_id', $student->id)->where('semester_id', $semester->id)->first();
                if ($existing) continue;

                $feeStructures = FeeStructure::where('academic_year_id', $semester->academic_year_id)
                    ->where(fn($q) => $q->whereNull('semester_id')->orWhere('semester_id', $semester->id))
                    ->where(fn($q) => $q->whereNull('program_id')->orWhere('program_id', $student->program_id))
                    ->active()->get();

                if ($feeStructures->isEmpty()) continue;

                $totalAmount = $feeStructures->sum('total_amount');
                $bill = StudentBill::create([
                    'student_id'       => $student->id,
                    'fee_structure_id' => $feeStructures->first()?->id,
                    'academic_year_id' => $semester->academic_year_id,
                    'semester_id'      => $semester->id,
                    'total_amount'     => $totalAmount,
                    'amount_paid'      => 0,
                    'balance'          => $totalAmount,
                    'due_date'         => $semester->start_date ? $semester->start_date->addWeeks(4) : null,
                    'status'           => 'unpaid',
                ]);

                foreach ($feeStructures as $fee) {
                    BillItem::create([
                        'student_bill_id' => $bill->id,
                        'fee_type'        => $fee->name,
                        'description'     => $fee->name,
                        'amount'          => $fee->total_amount,
                    ]);
                }
                $generated++;
            }
        });

        return back()->with('success', "Bills generated for {$generated} student(s).");
    }

    public function invoice(StudentBill $bill)
    {
        $bill->load(['student.user', 'student.program.department.faculty', 'feeStructure', 'items', 'payments', 'academicYear', 'semester']);
        $pdf = Pdf::loadView('finance.billing.invoice', compact('bill'))->setPaper('a4');
        return $pdf->download("invoice_{$bill->id}.pdf");
    }

    public function ajaxStudentBill(Request $request)
    {
        $bill = StudentBill::where('student_id', $request->student_id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->latest()
            ->first();

        if (!$bill) {
            return response()->json(null);
        }

        return response()->json([
            'id'           => $bill->id,
            'total_amount' => $bill->total_amount,
            'amount_paid'  => $bill->amount_paid,
            'balance'      => $bill->balance,
            'status'       => $bill->status,
        ]);
    }
}
