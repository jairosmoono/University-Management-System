<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;

class StudentApiController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['program.department.faculty', 'user'])
            ->active()
            ->when($request->program_id, fn($q) => $q->where('program_id', $request->program_id))
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('student_id', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            }))
            ->paginate(20);

        return response()->json($students);
    }

    public function show(Student $student)
    {
        $student->load(['user', 'program.department.faculty', 'guardian']);
        return response()->json($student);
    }

    public function courses(Student $student)
    {
        $registrations = CourseRegistration::with(['courseOffering.course', 'courseOffering.lecturer.user'])
            ->where('student_id', $student->id)
            ->where('status', 'registered')
            ->get();

        return response()->json($registrations);
    }

    public function results(Student $student)
    {
        $results = $student->finalResults()
            ->with(['courseOffering.course', 'semester', 'academicYear'])
            ->orderBy('academic_year_id')
            ->orderBy('semester_id')
            ->get();

        $gpaRecords = $student->gpaRecords()->with(['semester', 'academicYear'])->get();

        return response()->json([
            'results'     => $results,
            'gpa_records' => $gpaRecords,
            'cgpa'        => $student->latest_cgpa,
            'gpa'         => $student->latest_gpa,
        ]);
    }
}
