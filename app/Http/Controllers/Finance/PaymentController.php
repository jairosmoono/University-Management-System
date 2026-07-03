<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentBill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $isStudentView = auth()->user()->hasRole('student');

        $query = Payment::with(['studentBill.student.user', 'studentBill.semester', 'recordedBy']);

        if ($isStudentView) {
            $student = Student::where('user_id', auth()->id())->firstOrFail();
            $billIds = StudentBill::where('student_id', $student->id)->pluck('id');
            $query->whereIn('student_bill_id', $billIds);
        } else {
            if ($request->search) {
                $query->where(fn($q) =>
                    $q->where('reference_number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('studentBill.student', fn($s) => $s->where('student_id', 'like', '%' . $request->search . '%'))
                );
            }
        }

        if ($request->payment_method) $query->where('payment_method', $request->payment_method);
        if ($request->status)         $query->where('status', $request->status);
        if ($request->from_date)      $query->whereDate('payment_date', '>=', $request->from_date);
        if ($request->to_date)        $query->whereDate('payment_date', '<=', $request->to_date);

        $payments = $query->latest('payment_date')->paginate(20)->withQueryString();

        if ($isStudentView) {
            $base = Payment::whereIn('student_bill_id', $billIds ?? []);
            $stats = [
                'total_paid'  => (clone $base)->where('status', 'verified')->sum('amount'),
                'this_month'  => (clone $base)->where('status', 'verified')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
                'this_year'   => (clone $base)->where('status', 'verified')->whereYear('payment_date', now()->year)->sum('amount'),
                'count'       => (clone $base)->count(),
            ];
        } else {
            $stats = [
                'today'         => Payment::where('status', 'verified')->whereDate('payment_date', today())->sum('amount'),
                'this_month'    => Payment::where('status', 'verified')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount'),
                'this_year'     => Payment::where('status', 'verified')->whereYear('payment_date', now()->year)->sum('amount'),
                'pending_count' => Payment::where('status', 'pending')->count(),
            ];
        }

        return view('finance.payments.index', compact('payments', 'stats', 'isStudentView'));
    }

    public function create(?StudentBill $bill = null)
    {
        if ($bill && $bill->exists) {
            $bill->load(['student.user', 'semester', 'items']);
        } else {
            $bill = null;
        }
        return view('finance.payments.create', compact('bill'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_bill_id'       => 'required|exists:student_bills,id',
            'amount'                => 'required|numeric|min:0.01',
            'payment_method'        => 'required|string|max:50',
            'payment_date'          => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $payment = Payment::create([
            'student_bill_id'       => $request->student_bill_id,
            'reference_number'      => 'PAY/' . date('Ymd') . '/' . strtoupper(Str::random(6)),
            'amount'                => $request->amount,
            'payment_method'        => $request->payment_method,
            'payment_date'          => $request->payment_date,
            'transaction_reference' => $request->transaction_reference ?: 'TXN' . date('Ymd') . strtoupper(Str::random(8)),
            'notes'                 => $request->notes,
            'status'                => 'verified',
            'recorded_by'           => auth()->id(),
        ]);

        return redirect()->route('finance.payments.receipt', $payment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function verify(Payment $payment)
    {
        $payment->update(['status' => 'verified', 'verified_by' => auth()->id()]);
        return back()->with('success', 'Payment verified.');
    }

    public function reverse(Payment $payment)
    {
        $payment->update(['status' => 'reversed']);
        return back()->with('success', 'Payment reversed.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['studentBill.student.user', 'studentBill.student.program', 'studentBill.semester', 'recordedBy']);

        $settingsRaw = \Illuminate\Support\Facades\Storage::exists('settings.json')
            ? json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true)
            : [];
        $uniName = $settingsRaw['university_name'] ?? config('app.name', 'University Management System');

        $logoSrc = null;
        if (!empty($settingsRaw['logo_path'])) {
            $logoFile = storage_path('app/public/' . $settingsRaw['logo_path']);
            if (file_exists($logoFile)) {
                $mime    = mime_content_type($logoFile);
                $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        }

        $pdf = Pdf::loadView('finance.payments.receipt', compact('payment', 'uniName', 'logoSrc'))->setPaper('a4');
        $filename = 'receipt_' . str_replace(['/', '\\'], '-', $payment->reference_number) . '.pdf';
        return request()->boolean('dl') ? $pdf->download($filename) : $pdf->stream($filename);
    }
}
