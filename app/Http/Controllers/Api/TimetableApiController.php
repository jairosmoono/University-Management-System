<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Http\Request;

class TimetableApiController extends Controller
{
    public function index(Request $request)
    {
        $timetable = Timetable::with([
                'courseOffering.course',
                'courseOffering.lecturer.user',
            ])
            ->when($request->program_id, fn($q) => $q->whereHas('courseOffering.coursePrograms', fn($p) => $p->where('program_id', $request->program_id)))
            ->when($request->semester_id, fn($q) => $q->whereHas('courseOffering', fn($o) => $o->where('semester_id', $request->semester_id)))
            ->when($request->day, fn($q) => $q->where('day_of_week', $request->day))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json($timetable);
    }

    public function byProgram(Program $program, Request $request)
    {
        $semesterId = $request->semester_id ?? optional(Semester::current())->id;

        $timetable = Timetable::with(['courseOffering.course', 'courseOffering.lecturer.user'])
            ->whereHas('courseOffering', function ($q) use ($program, $semesterId) {
                $q->where('semester_id', $semesterId)
                  ->whereHas('course.programs', fn($p) => $p->where('programs.id', $program->id));
            })
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'program'   => $program->load('department'),
            'semester'  => $semesterId,
            'timetable' => $timetable,
        ]);
    }
}
