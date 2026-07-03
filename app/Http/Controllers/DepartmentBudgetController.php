<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\BudgetTransaction;
use App\Models\Department;
use App\Models\DepartmentBudget;
use Illuminate\Http\Request;

class DepartmentBudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = DepartmentBudget::with(['department', 'academicYear', 'approvedBy']);

        if ($request->department_id) $query->where('department_id', $request->department_id);
        if ($request->academic_year_id) $query->where('academic_year_id', $request->academic_year_id);
        if ($request->status) $query->where('status', $request->status);

        $budgets      = $query->latest()->paginate(20);
        $departments  = Department::active()->orderBy('name')->get();
        $academicYears= AcademicYear::orderBy('start_date', 'desc')->get();

        $totals = DepartmentBudget::selectRaw('
            SUM(total_budget) as grand_total,
            COUNT(*) as budget_count
        ')->first();

        return view('budgets.index', compact('budgets', 'departments', 'academicYears', 'totals'));
    }

    public function create()
    {
        $departments   = Department::active()->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('budgets.create', compact('departments', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id'    => 'required|exists:departments,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'fiscal_year'      => 'required|string|max:20',
            'total_budget'     => 'required|numeric|min:0',
            'description'      => 'nullable|string|max:1000',
        ]);

        $budget = DepartmentBudget::create($request->only(
            'department_id', 'academic_year_id', 'fiscal_year', 'total_budget', 'description'
        ) + ['status' => 'draft']);

        return redirect()->route('academic.budgets.show', $budget)
            ->with('success', 'Budget created successfully.');
    }

    public function show(DepartmentBudget $budget)
    {
        $budget->load(['department', 'academicYear', 'approvedBy', 'transactions.recordedBy']);

        $summary = [
            'total_budget'    => (float)$budget->total_budget,
            'total_expenses'  => $budget->total_expenses,
            'total_allocated' => $budget->total_allocated,
            'remaining'       => $budget->remaining_budget,
            'used_percent'    => $budget->used_percent,
            'by_category'     => $budget->transactions()
                ->where('type', 'expense')
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get(),
        ];

        $categories = ['Staff Costs', 'Equipment', 'Supplies', 'Travel', 'Research', 'Infrastructure', 'Training', 'Miscellaneous'];

        return view('budgets.show', compact('budget', 'summary', 'categories'));
    }

    public function addTransaction(Request $request, DepartmentBudget $budget)
    {
        $request->validate([
            'type'             => 'required|in:allocation,expense,transfer,adjustment',
            'category'         => 'required|string|max:80',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'nullable|string|max:500',
            'reference_no'     => 'nullable|string|max:60',
            'transaction_date' => 'required|date',
        ]);

        BudgetTransaction::create([
            'department_budget_id' => $budget->id,
            'type'                 => $request->type,
            'category'             => $request->category,
            'amount'               => $request->amount,
            'description'          => $request->description,
            'reference_no'         => $request->reference_no,
            'transaction_date'     => $request->transaction_date,
            'recorded_by'          => auth()->id(),
        ]);

        return back()->with('success', ucfirst($request->type) . ' recorded successfully.');
    }

    public function approve(DepartmentBudget $budget)
    {
        $budget->update([
            'status'      => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Budget approved and activated.');
    }

    public function destroy(DepartmentBudget $budget)
    {
        if ($budget->status !== 'draft') {
            return back()->with('error', 'Only draft budgets can be deleted.');
        }
        $budget->delete();
        return redirect()->route('academic.budgets.index')->with('success', 'Budget deleted.');
    }
}
