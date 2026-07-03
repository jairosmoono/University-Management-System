<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinalResult;
use App\Models\GpaRecord;
use App\Models\Student;
use App\Models\Semester;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ResultApiController extends Controller
{
    public function index(Request $request)
    {
        $results = FinalResult::with(['student.user', 'courseOffering.course', 'semester', 'academicYear'])
            ->when($request->student_id, fn($q) => $q->where('student_id', $request->student_id))
            ->when($request->semester_id, fn($q) => $q->where('semester_id', $request->semester_id))
            ->when($request->academic_year_id, fn($q) => $q->where('academic_year_id', $request->academic_year_id))
            ->paginate(50);

        return response()->json($results);
    }

    public function studentTranscript(Student $student)
    {
        $results = FinalResult::with(['courseOffering.course', 'semester', 'academicYear'])
            ->where('student_id', $student->id)
            ->orderBy('academic_year_id')
            ->orderBy('semester_id')
            ->get();

        $gpaRecords = GpaRecord::with(['semester', 'academicYear'])
            ->where('student_id', $student->id)
            ->orderBy('academic_year_id')
            ->orderBy('semester_id')
            ->get();

        return response()->json([
            'student'     => $student->load(['user', 'program.department.faculty']),
            'results'     => $results,
            'gpa_records' => $gpaRecords,
        ]);
    }

    public function gpaRecords(Request $request)
    {
        $records = GpaRecord::with(['student.user', 'student.program', 'semester', 'academicYear'])
            ->when($request->student_id, fn($q) => $q->where('student_id', $request->student_id))
            ->when($request->semester_id, fn($q) => $q->where('semester_id', $request->semester_id))
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json($records);
    }
}
