<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamType;
use App\Models\GradeScale;
use Illuminate\Http\Request;

class AcademicSettingController extends Controller
{
    public function index()
    {
        $gradeScales = GradeScale::orderBy('sort_order')->orderBy('min_score', 'desc')->get();
        $examTypes   = ExamType::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.academic-settings.index', compact('gradeScales', 'examTypes'));
    }

    // ── Grade Scales ──────────────────────────────────────────────────────────

    public function storeGrade(Request $request)
    {
        $data = $request->validate([
            'grade'        => 'required|string|max:5|unique:grade_scales,grade',
            'min_score'    => 'required|numeric|min:0|max:100',
            'grade_points' => 'required|numeric|min:0|max:4',
            'label'        => 'nullable|string|max:60',
            'is_pass'      => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);
        $data['is_pass'] = $request->boolean('is_pass');

        GradeScale::create($data);
        GradeScale::clearCache();

        return back()->with('success', "Grade '{$data['grade']}' added.");
    }

    public function updateGrade(Request $request, GradeScale $gradeScale)
    {
        $data = $request->validate([
            'grade'        => 'required|string|max:5|unique:grade_scales,grade,' . $gradeScale->id,
            'min_score'    => 'required|numeric|min:0|max:100',
            'grade_points' => 'required|numeric|min:0|max:4',
            'label'        => 'nullable|string|max:60',
            'is_pass'      => 'boolean',
            'sort_order'   => 'integer|min:0',
        ]);
        $data['is_pass'] = $request->boolean('is_pass');

        $gradeScale->update($data);
        GradeScale::clearCache();

        return back()->with('success', "Grade '{$gradeScale->grade}' updated.");
    }

    public function destroyGrade(GradeScale $gradeScale)
    {
        $gradeScale->delete();
        GradeScale::clearCache();
        return back()->with('success', 'Grade scale entry deleted.');
    }

    // ── Exam Types ────────────────────────────────────────────────────────────

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:exam_types,code|regex:/^[a-z0-9_]+$/',
            'category'    => 'required|in:ca,exam,other',
            'description' => 'nullable|string|max:255',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        ExamType::create($data);
        return back()->with('success', "Exam type '{$data['name']}' added.");
    }

    public function updateType(Request $request, ExamType $examType)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:exam_types,code,' . $examType->id . '|regex:/^[a-z0-9_]+$/',
            'category'    => 'required|in:ca,exam,other',
            'description' => 'nullable|string|max:255',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        $examType->update($data);
        return back()->with('success', "Exam type '{$examType->name}' updated.");
    }

    public function destroyType(ExamType $examType)
    {
        $examType->delete();
        return back()->with('success', 'Exam type deleted.');
    }
}
