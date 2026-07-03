<?php
namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['program', 'academicYear', 'semester'])->withCount('feeItems');
        if ($request->academic_year_id) $query->where('academic_year_id', $request->academic_year_id);
        if ($request->admission_type)    $query->where('admission_type', $request->admission_type);
        $feeStructures = $query->orderBy('name')->paginate(20);
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $programs      = Program::active()->orderBy('name')->get();
        $semesters     = Semester::orderBy('start_date', 'desc')->get();
        return view('finance.fee-structures.index', compact('feeStructures', 'academicYears', 'programs', 'semesters'));
    }

    public function create()
    {
        $programs = Program::active()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('finance.fee-structures.create', compact('programs', 'academicYears', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'total_amount'     => 'required|numeric|min:0',
            'admission_type'     => 'required|in:full-time,part-time,distance,online,all',
        ]);

        $fs = FeeStructure::create($request->only(['name', 'academic_year_id', 'semester_id', 'program_id', 'admission_type', 'total_amount', 'status']));
        $this->syncFeeItems($fs, $request->input('items', []));
        return redirect()->route('finance.fee-structures.index')
            ->with('success', 'Fee structure created successfully.');
    }

    public function show(FeeStructure $feeStructure)
    {
        return view('finance.fee-structures.show', compact('feeStructure'));
    }

    public function edit(FeeStructure $feeStructure)
    {
        $programs = Program::active()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('finance.fee-structures.create', compact('feeStructure', 'programs', 'academicYears', 'semesters'));
    }

    public function update(Request $request, FeeStructure $feeStructure)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'total_amount'     => 'required|numeric|min:0',
            'admission_type'     => 'required|in:full-time,part-time,distance,online,all',
        ]);

        $feeStructure->update($request->only(['name', 'academic_year_id', 'semester_id', 'program_id', 'admission_type', 'total_amount', 'status']));
        $this->syncFeeItems($feeStructure, $request->input('items', []));
        return redirect()->route('finance.fee-structures.index')
            ->with('success', 'Fee structure updated successfully.');
    }

    private function syncFeeItems(FeeStructure $fs, array $items): void
    {
        $fs->feeItems()->delete();
        foreach ($items as $item) {
            $amount = floatval($item['amount'] ?? 0);
            if ($amount <= 0 && empty($item['description'])) continue;
            $fs->feeItems()->create([
                'fee_type'     => $item['fee_type'] ?? 'Other',
                'description'  => $item['description'] ?? '',
                'amount'       => $amount,
                'is_mandatory' => !empty($item['is_mandatory']),
            ]);
        }
    }

    public function json(FeeStructure $feeStructure)
    {
        return response()->json($feeStructure->load('feeItems'));
    }

    public function clone(FeeStructure $feeStructure)
    {
        $new = $feeStructure->replicate();
        $new->name = $feeStructure->name . ' (Copy)';
        $new->status = 'inactive';
        $new->save();
        return redirect()->route('finance.fee-structures.edit', $new)
            ->with('success', 'Fee structure cloned. Update details and activate.');
    }

    public function destroy(FeeStructure $feeStructure)
    {
        $feeStructure->delete();
        return redirect()->route('finance.fee-structures.index')
            ->with('success', 'Fee structure deleted.');
    }
}
