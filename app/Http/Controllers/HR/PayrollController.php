<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;
use App\Models\Notification;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Department;
use App\Models\PayrollConfiguration;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        // Shared filter closure — applied to both the list and all stat queries
        $applyFilters = function ($q) use ($request) {
            if ($request->filled('month'))         $q->where('month', $request->month);
            if ($request->filled('year'))          $q->where('year', $request->year);
            if ($request->filled('department_id')) {
                $q->whereHas('employee', fn($e) => $e->where('department_id', $request->department_id));
            }
        };

        $query = Payroll::with(['employee.user', 'employee.department']);
        $applyFilters($query);
        if ($request->filled('status')) $query->where('status', $request->status);
        $payrolls    = $query->latest()->paginate(20);
        $departments = Department::all();

        // Stats base — month/year/department filters only, no status filter
        // so status-breakdown counts always reflect the full picture for the period
        $statsBase = fn() => tap(Payroll::query(), $applyFilters);

        $stats = [
            'total_payroll'    => (clone $statsBase())->when($request->filled('status'), fn($q) => $q->where('status', $request->status))->sum('net_pay'),
            'total_deductions' => (clone $statsBase())->when($request->filled('status'), fn($q) => $q->where('status', $request->status))->sum('deductions'),
            'total_basic'      => (clone $statsBase())->when($request->filled('status'), fn($q) => $q->where('status', $request->status))->sum('basic_salary'),
            'pending'          => (clone $statsBase())->where('status', 'pending')->count(),
            'processed'        => (clone $statsBase())->whereIn('status', ['processed', 'paid'])->count(),
            'total_employees'  => (clone $statsBase())->when($request->filled('status'), fn($q) => $q->where('status', $request->status))->count(),
        ];

        return view('hr.payroll.index', compact('payrolls', 'departments', 'stats'));
    }

    public function generate(Request $request)
    {
        $request->validate(['month' => 'required|integer|between:1,12', 'year' => 'required|integer']);
        $cfg = PayrollConfiguration::asArray();

        $query = Employee::with(['allowances' => fn($q) => $q->where('is_active', true),
                                 'deductions'  => fn($q) => $q->where('is_active', true)->where('is_recurring', true)])->active();
        if ($request->department_id) $query->where('department_id', $request->department_id);
        $employees = $query->get();

        // Pre-load approved salary advances grouped by employee
        $advanceDeductions = SalaryAdvance::where('status', 'approved')
            ->get()->keyBy('employee_id');

        // Resolve payment date: configured day clamped to last day of the selected month
        $payrollDay  = max(1, min(31, (int) ($cfg['payroll_date'] ?? 25)));
        $daysInMonth = (int) date('t', mktime(0, 0, 0, $request->month, 1, $request->year));
        $paymentDate = \Carbon\Carbon::createFromDate($request->year, $request->month, min($payrollDay, $daysInMonth));

        $generated = 0;
        foreach ($employees as $emp) {
            $exists = Payroll::where('employee_id', $emp->id)
                ->where('month', $request->month)->where('year', $request->year)->exists();
            if ($exists) continue;

            $basic = (float) $emp->basic_salary;

            // Allowances from employee_allowances
            $allowances = 0;
            foreach ($emp->allowances as $al) {
                $allowances += $al->percentage > 0
                    ? $basic * ($al->percentage / 100)
                    : (float) $al->amount;
            }
            // If no allowances configured, fall back to 0
            $gross = $basic + $allowances;

            // Statutory deductions
            $paye  = $this->calculatePAYEFromConfig($gross, $cfg);
            $napsa = min($gross * (floatval($cfg['napsa_rate'] ?? 5) / 100), floatval($cfg['napsa_cap'] ?? 1073));
            $nhimaRate = floatval($cfg['nhima_rate'] ?? 1);
            $nhimaCap  = floatval($cfg['nhima_cap'] ?? 0);
            $nhima = $gross * ($nhimaRate / 100);
            if ($nhimaCap > 0) $nhima = min($nhima, $nhimaCap);

            // Salary advance monthly repayment
            $advanceDeduction = 0;
            if (isset($advanceDeductions[$emp->id])) {
                $adv = $advanceDeductions[$emp->id];
                $months = max(1, (int) $adv->repayment_months);
                $advanceDeduction = round((float) $adv->amount_approved / $months, 2);
            }

            // Custom recurring deductions
            $customDeductions = $emp->deductions->sum(fn($d) => (float) $d->amount);

            $totalDeductions = round($paye + $napsa + $nhima + $advanceDeduction + $customDeductions, 2);
            $net = round($gross - $totalDeductions, 2);

            Payroll::create([
                'employee_id'  => $emp->id,
                'month'        => $request->month,
                'year'         => $request->year,
                'basic_salary' => round($basic, 2),
                'allowances'   => round($allowances, 2),
                'deductions'   => $totalDeductions,
                'tax'          => round($paye, 2),
                'net_pay'      => $net,
                'status'        => 'pending',
                'payment_date'  => $paymentDate,
                'processed_by'  => auth()->id(),
                'notes'         => $this->buildNotes($paye, $napsa, $nhima, $advanceDeduction, $customDeductions),
            ]);
            $generated++;
        }
        return back()->with('success', "Payroll generated for {$generated} employee(s).");
    }

    private function buildNotes(float $paye, float $napsa, float $nhima, float $advance, float $custom): string
    {
        $parts = [];
        if ($paye   > 0) $parts[] = "PAYE: " . number_format($paye, 2);
        if ($napsa  > 0) $parts[] = "NAPSA: " . number_format($napsa, 2);
        if ($nhima  > 0) $parts[] = "NHIMA: " . number_format($nhima, 2);
        if ($advance > 0) $parts[] = "Advance repayment: " . number_format($advance, 2);
        if ($custom > 0) $parts[] = "Other deductions: " . number_format($custom, 2);
        return implode(' | ', $parts);
    }

    public function process(Payroll $payroll)
    {
        $cfg         = PayrollConfiguration::asArray();
        $payrollDay  = max(1, min(31, (int) ($cfg['payroll_date'] ?? 25)));
        $daysInMonth = (int) date('t', mktime(0, 0, 0, $payroll->month, 1, $payroll->year));
        $paymentDate = \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, min($payrollDay, $daysInMonth));

        $payroll->load('employee.user');
        $payroll->update(['status' => 'processed', 'payment_date' => $paymentDate]);

        if ($userId = optional(optional($payroll->employee)->user)->id) {
            Notification::send(
                $userId, 'payment',
                'Payroll Processed',
                'Your payroll for ' . date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) . ' has been processed. Net pay: ' . formatCurrency($payroll->net_pay) . '.',
                [], route('hr.payroll.index')
            );
        }

        return back()->with('success', 'Payroll marked as processed.');
    }

    public function salarySchedule(Request $request)
    {
        $month        = (int) ($request->month ?? date('n'));
        $year         = (int) ($request->year  ?? date('Y'));
        $departmentId = $request->department_id;

        $payrolls = Payroll::with(['employee.user', 'employee.department'])
            ->where('month', $month)
            ->where('year',  $year)
            ->whereIn('status', ['processed', 'paid'])
            ->when($departmentId, fn($q) => $q->whereHas('employee', fn($e) => $e->where('department_id', $departmentId)))
            ->get()
            ->sortBy(fn($p) => optional(optional($p->employee)->user)->name);

        $departments = Department::orderBy('name')->get();
        $department  = $departmentId ? Department::find($departmentId) : null;

        $totalNet    = $payrolls->sum('net_pay');
        $totalCount  = $payrolls->count();

        // University branding
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

        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('hr.payroll.salary-schedule-pdf', compact(
                'payrolls', 'totalNet', 'totalCount',
                'month', 'year', 'department', 'logoSrc', 'uniName'
            ))->setPaper('a4');
            return $pdf->download("salary_schedule_" . date('F_Y', mktime(0,0,0,$month,1,$year)) . ".pdf");
        }

        return view('hr.payroll.salary-schedule', compact(
            'payrolls', 'departments', 'department',
            'totalNet', 'totalCount', 'month', 'year'
        ));
    }

    public function report(Request $request)
    {
        $month        = (int) ($request->month ?? date('n'));
        $year         = (int) ($request->year  ?? date('Y'));
        $departmentId = $request->department_id;
        $status       = $request->status;

        $query = Payroll::with(['employee.user', 'employee.department'])
            ->where('month', $month)
            ->where('year',  $year)
            ->when($departmentId, fn($q) => $q->whereHas('employee', fn($e) => $e->where('department_id', $departmentId)))
            ->when($status,       fn($q) => $q->where('status', $status));

        $payrolls = $query->get()->sortBy(fn($p) => optional(optional($p->employee)->user)->name);

        $summary = [
            'total_employees' => $payrolls->count(),
            'total_basic'     => $payrolls->sum('basic_salary'),
            'total_allowances'=> $payrolls->sum('allowances'),
            'total_deductions'=> $payrolls->sum('deductions'),
            'total_net'       => $payrolls->sum('net_pay'),
            'total_tax'       => $payrolls->sum('tax'),
        ];

        $byDepartment = $payrolls
            ->groupBy(fn($p) => optional(optional($p->employee)->department)->name ?? 'Unknown')
            ->map(fn($g) => [
                'count'      => $g->count(),
                'total_basic'=> $g->sum('basic_salary'),
                'total_net'  => $g->sum('net_pay'),
            ])->sortByDesc(fn($d) => $d['total_net']);

        $department = $departmentId ? Department::find($departmentId) : null;

        // University branding
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

        $pdf = Pdf::loadView('hr.payroll.report-pdf', compact(
            'payrolls', 'summary', 'byDepartment',
            'month', 'year', 'status', 'department',
            'logoSrc', 'uniName'
        ))->setPaper('a4', 'landscape');

        $label = date('F_Y', mktime(0, 0, 0, $month, 1, $year));
        return $pdf->download("payroll_report_{$label}.pdf");
    }

    public function slip(Payroll $payroll)
    {
        $payroll->load([
            'employee.user',
            'employee.department',
            'employee.allowances' => fn($q) => $q->where('is_active', true),
            'employee.deductions' => fn($q) => $q->where('is_active', true),
        ]);

        // Load university branding from settings.json
        $settingsRaw = \Illuminate\Support\Facades\Storage::exists('settings.json')
            ? json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true)
            : [];
        $uniName = $settingsRaw['university_name'] ?? config('app.university.name', 'University Management System');

        $logoSrc = null;
        if (!empty($settingsRaw['logo_path'])) {
            $logoFile = storage_path('app/public/' . $settingsRaw['logo_path']);
            if (file_exists($logoFile)) {
                $mime    = mime_content_type($logoFile);
                $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        }

        $cfg   = PayrollConfiguration::asArray();
        $basic = (float) $payroll->basic_salary;

        // Build allowance breakdown
        $allowanceRows = [];
        foreach ($payroll->employee->allowances as $al) {
            $amt = $al->percentage > 0
                ? round($basic * ($al->percentage / 100), 2)
                : (float) $al->amount;
            $label = $al->description ?: ucfirst(str_replace('_', ' ', $al->allowance_type));
            $allowanceRows[] = ['label' => $label, 'amount' => $amt];
        }

        // Build deduction breakdown
        $gross = $basic + (float) $payroll->allowances;

        $paye  = $this->calculatePAYEFromConfig($gross, $cfg);
        $napsa = min($gross * (floatval($cfg['napsa_rate'] ?? 5) / 100), floatval($cfg['napsa_cap'] ?? 1073));
        $nhimaRate = floatval($cfg['nhima_rate'] ?? 1);
        $nhimaCap  = floatval($cfg['nhima_cap'] ?? 0);
        $nhima = $gross * ($nhimaRate / 100);
        if ($nhimaCap > 0) $nhima = min($nhima, $nhimaCap);

        $adv = SalaryAdvance::where('employee_id', $payroll->employee_id)
                   ->where('status', 'approved')->first();
        $advAmt = 0;
        if ($adv) {
            $advAmt = round((float) $adv->amount_approved / max(1, (int) $adv->repayment_months), 2);
        }

        $deductionRows = [];
        if ($paye  > 0) $deductionRows[] = ['label' => 'Income Tax (PAYE)',   'amount' => round($paye, 2)];
        if ($napsa > 0) $deductionRows[] = ['label' => 'NAPSA Contribution',  'amount' => round($napsa, 2)];
        if ($nhima > 0) $deductionRows[] = ['label' => 'NHIMA Contribution',  'amount' => round($nhima, 2)];
        if ($advAmt> 0) $deductionRows[] = ['label' => 'Salary Advance Repayment', 'amount' => $advAmt];

        foreach ($payroll->employee->deductions as $ded) {
            $label = $ded->description ?: ucfirst(str_replace('_', ' ', $ded->deduction_type));
            $deductionRows[] = ['label' => $label, 'amount' => (float) $ded->amount];
        }

        $pdf = Pdf::loadView('hr.payroll.slip', compact(
            'payroll', 'allowanceRows', 'deductionRows', 'logoSrc', 'uniName'
        ))->setPaper('a5');

        return $pdf->download("payslip_{$payroll->employee?->employee_id}_{$payroll->month}_{$payroll->year}.pdf");
    }

    private function calculatePAYEFromConfig(float $gross, array $cfg): float
    {
        $b1 = floatval($cfg['paye_band1_max']  ?? 4800);
        $b2 = floatval($cfg['paye_band2_max']  ?? 6900);
        $b3 = floatval($cfg['paye_band3_max']  ?? 9200);
        $r2 = floatval($cfg['paye_band2_rate'] ?? 25)  / 100;
        $r3 = floatval($cfg['paye_band3_rate'] ?? 30)  / 100;
        $r4 = floatval($cfg['paye_band4_rate'] ?? 37.5)/ 100;

        $tax = 0;
        if ($gross <= $b1) return 0;
        if ($gross <= $b2) return ($gross - $b1) * $r2;
        $tax += ($b2 - $b1) * $r2;
        if ($gross <= $b3) return $tax + ($gross - $b2) * $r3;
        $tax += ($b3 - $b2) * $r3;
        $tax += ($gross - $b3) * $r4;
        return round($tax, 2);
    }
}
