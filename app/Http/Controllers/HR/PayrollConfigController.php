<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDeduction;
use App\Models\PayrollConfiguration;
use App\Models\PayrollItemType;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayrollConfigController extends Controller
{
    public function index()
    {
        $configs   = PayrollConfiguration::orderBy('group')->orderBy('key')->get()->groupBy('group');
        $employees = Employee::with(['user', 'allowances', 'deductions'])->active()->get();
        $advances  = SalaryAdvance::with('employee.user')
                        ->where('status', 'approved')
                        ->get();

        $allItemTypes     = PayrollItemType::orderBy('category')->orderBy('name')->get();
        $allowanceTypes   = $allItemTypes->where('category', 'allowance');
        $deductionTypes   = $allItemTypes->where('category', 'deduction');

        return view('hr.payroll.config', compact(
            'configs', 'employees', 'advances', 'allowanceTypes', 'deductionTypes', 'allItemTypes'
        ));
    }

    public function updateGlobal(Request $request)
    {
        $data = $request->validate([
            'configs'   => 'required|array',
            'configs.*' => 'nullable|numeric',
        ]);

        foreach ($data['configs'] as $key => $value) {
            PayrollConfiguration::where('key', $key)->update(['value' => $value ?? 0]);
        }

        return back()->with('success', 'Payroll configurations saved successfully.');
    }

    // ── Allowances ────────────────────────────────────────────────────────────

    public function storeAllowance(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'allowance_type' => 'required|string|max:60',
            'description'    => 'nullable|string|max:255',
            'percentage'     => 'nullable|numeric|min:0|max:100',
            'amount'         => 'nullable|numeric|min:0',
        ]);

        $usePercent = $request->filled('percentage') && floatval($request->percentage) > 0;

        EmployeeAllowance::create([
            'employee_id'    => $request->employee_id,
            'allowance_type' => $request->allowance_type,
            'description'    => $request->description,
            'percentage'     => $usePercent ? $request->percentage : 0,
            'amount'         => $usePercent ? 0 : ($request->amount ?? 0),
            'is_active'      => true,
        ]);

        return back()->with('success', 'Allowance added.');
    }

    public function updateAllowance(Request $request, EmployeeAllowance $employeeAllowance)
    {
        $request->validate([
            'allowance_type' => 'required|string|max:60',
            'description'    => 'nullable|string|max:255',
            'percentage'     => 'nullable|numeric|min:0|max:100',
            'amount'         => 'nullable|numeric|min:0',
            'is_active'      => 'boolean',
        ]);

        $usePercent = $request->filled('percentage') && floatval($request->percentage) > 0;

        $employeeAllowance->update([
            'allowance_type' => $request->allowance_type,
            'description'    => $request->description,
            'percentage'     => $usePercent ? $request->percentage : 0,
            'amount'         => $usePercent ? 0 : ($request->amount ?? 0),
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Allowance updated.');
    }

    public function destroyAllowance(EmployeeAllowance $employeeAllowance)
    {
        $employeeAllowance->delete();
        return back()->with('success', 'Allowance removed.');
    }

    // ── Custom Deductions ─────────────────────────────────────────────────────

    public function storeDeduction(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'deduction_type' => 'required|string|max:60',
            'description'    => 'nullable|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'is_recurring'   => 'boolean',
        ]);

        EmployeeDeduction::create([
            'employee_id'    => $request->employee_id,
            'deduction_type' => $request->deduction_type,
            'description'    => $request->description,
            'amount'         => $request->amount,
            'is_recurring'   => $request->boolean('is_recurring', true),
            'is_active'      => true,
        ]);

        return back()->with('success', 'Deduction added.');
    }

    public function updateDeduction(Request $request, EmployeeDeduction $employeeDeduction)
    {
        $request->validate([
            'deduction_type' => 'required|string|max:60',
            'description'    => 'nullable|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'is_recurring'   => 'boolean',
            'is_active'      => 'boolean',
        ]);

        $employeeDeduction->update([
            'deduction_type' => $request->deduction_type,
            'description'    => $request->description,
            'amount'         => $request->amount,
            'is_recurring'   => $request->boolean('is_recurring', true),
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Deduction updated.');
    }

    public function destroyDeduction(EmployeeDeduction $employeeDeduction)
    {
        $employeeDeduction->delete();
        return back()->with('success', 'Deduction removed.');
    }

    // ── Bank Accounts ─────────────────────────────────────────────────────────

    public function updateBankAccount(Request $request, Employee $employee)
    {
        $request->validate([
            'bank_name'    => 'nullable|string|max:100',
            'bank_account' => 'nullable|string|max:50',
            'sort_code'    => 'nullable|string|max:20',
            'bank_branch'  => 'nullable|string|max:100',
        ]);

        $employee->update([
            'bank_name'    => $request->bank_name  ?: null,
            'bank_account' => $request->bank_account ?: null,
            'sort_code'    => $request->sort_code ?: null,
            'bank_branch'  => $request->bank_branch ?: null,
        ]);

        return back()->with('success', 'Bank details updated for ' . optional($employee->user)->name . '.');
    }

    // ── Item Types (allowance / deduction) ────────────────────────────────────

    public function storeItemType(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'category' => 'required|in:allowance,deduction',
        ]);

        $slug = Str::slug($request->name, '_');

        // Ensure slug uniqueness
        $base = $slug;
        $i = 1;
        while (PayrollItemType::where('slug', $slug)->exists()) {
            $slug = $base . '_' . $i++;
        }

        PayrollItemType::create([
            'name'      => $request->name,
            'slug'      => $slug,
            'category'  => $request->category,
            'is_active' => true,
        ]);

        return back()->with('success', ucfirst($request->category) . ' type "' . $request->name . '" added.');
    }

    public function updateItemType(Request $request, PayrollItemType $payrollItemType)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $payrollItemType->update([
            'name'      => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Type updated.');
    }

    public function destroyItemType(PayrollItemType $payrollItemType)
    {
        // Check if in use
        if ($payrollItemType->category === 'allowance') {
            $inUse = EmployeeAllowance::where('allowance_type', $payrollItemType->slug)->exists();
        } else {
            $inUse = EmployeeDeduction::where('deduction_type', $payrollItemType->slug)->exists();
        }

        if ($inUse) {
            return back()->with('error', 'Cannot delete: this type is already in use by employee records.');
        }

        $payrollItemType->delete();
        return back()->with('success', 'Type deleted.');
    }
}
