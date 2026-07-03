<?php
namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Payment;
use App\Models\StudentBill;
use App\Models\GpaRecord;
use App\Models\AttendanceRecord;
use App\Models\FinalResult;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\RoomAllocation;
use App\Models\Scholarship;
use App\Models\ScholarshipAward;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $byFaculty = Faculty::active()->get()->map(function ($f) {
            return (object)['name' => $f->name, 'students_count' => $f->studentsQuery()->count()];
        });
        $stats = [
            'total_students' => Student::active()->count(),
            'total_staff'    => Staff::active()->count(),
            'total_revenue'  => Payment::where('status', 'verified')->sum('amount'),
            'outstanding'    => StudentBill::sum('balance'),
            'by_faculty'     => $byFaculty,
            'by_program'     => Program::withCount(['students as students_count' => fn($q) => $q->active()])->with('department')->active()->take(10)->get(),
        ];
        return view('reports.index', compact('stats'));
    }

    public function students(Request $request)
    {
        $query = Student::with(['user', 'program.department.faculty']);

        // Status filter — default to active only when no status param given
        $status = $request->input('status', 'active');
        if ($status && $status !== 'all') $query->where('students.status', $status);

        if ($request->faculty_id)    $query->byFaculty($request->faculty_id);
        if ($request->program_id)    $query->where('program_id', $request->program_id);
        if ($request->gender)        $query->where('gender', $request->gender);
        if ($request->admission_type)  $query->where('admission_type', $request->admission_type);
        if ($request->year_of_study) $query->where('year_of_study', $request->year_of_study);
        if ($request->enrollment_year) {
            $query->whereYear('enrollment_date', $request->enrollment_year);
        }

        $students  = $query->orderBy('program_id')->orderBy('id')->paginate(50)->withQueryString();
        $faculties = Faculty::active()->get();
        $programs  = Program::active()->with('department')->orderBy('name')->get();

        // Summary stats on the filtered set (un-paginated count queries)
        $baseQuery = Student::query();
        if ($status && $status !== 'all') $baseQuery->where('students.status', $status);
        if ($request->faculty_id)    $baseQuery->byFaculty($request->faculty_id);
        if ($request->program_id)    $baseQuery->where('program_id', $request->program_id);
        if ($request->gender)        $baseQuery->where('gender', $request->gender);
        if ($request->admission_type)  $baseQuery->where('admission_type', $request->admission_type);
        if ($request->year_of_study) $baseQuery->where('year_of_study', $request->year_of_study);
        if ($request->enrollment_year) $baseQuery->whereYear('enrollment_date', $request->enrollment_year);

        $totalFiltered  = (clone $baseQuery)->count();
        $totalDropouts  = Student::where('status', 'dropped_out')->count();
        $byGender       = (clone $baseQuery)->selectRaw('gender, COUNT(*) as cnt')->groupBy('gender')->pluck('cnt', 'gender');
        $byYear         = (clone $baseQuery)->selectRaw('year_of_study, COUNT(*) as cnt')->groupBy('year_of_study')->orderBy('year_of_study')->pluck('cnt', 'year_of_study');
        $byType         = (clone $baseQuery)->selectRaw('admission_type, COUNT(*) as cnt')->groupBy('admission_type')->pluck('cnt', 'admission_type');

        // Program breakdown for the summary table
        $programBreakdown = (clone $baseQuery)
            ->selectRaw('program_id, COUNT(*) as cnt')
            ->groupBy('program_id')
            ->orderByDesc('cnt')
            ->with('program.department')
            ->get()
            ->map(fn($r) => [
                'program' => Program::with('department')->find($r->program_id),
                'count'   => $r->cnt,
            ]);

        $stats = compact('totalFiltered', 'totalDropouts', 'byGender', 'byYear', 'byType', 'programBreakdown');

        return view('reports.students', compact('students', 'faculties', 'programs', 'stats', 'status'));
    }

    public function academic(Request $request)
    {
        $semester = Semester::find($request->semester_id) ?? Semester::where('is_current', true)->first();
        $gpaRecords = GpaRecord::where('semester_id', $semester?->id)->with('student.program')->orderBy('cgpa', 'desc')->paginate(30);
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('reports.academic', compact('gpaRecords', 'semester', 'semesters'));
    }

    public function finance(Request $request)
    {
        $year = $request->year ?? date('Y');
        $monthlyRevenue = Payment::where('status', 'verified')->whereYear('payment_date', $year)->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')->groupBy('month')->get();
        $byMethod = Payment::where('status', 'verified')->whereYear('payment_date', $year)->selectRaw('payment_method, SUM(amount) as total')->groupBy('payment_method')->get();
        return view('reports.finance', compact('monthlyRevenue', 'byMethod', 'year'));
    }

    public function attendance(Request $request)
    {
        return view('reports.attendance');
    }

    public function loginActivity(Request $request)
    {
        $query = User::whereNotNull('last_login_at')->orderBy('last_login_at', 'desc');

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        if ($request->role) {
            $query->role($request->role);
        }
        if ($request->date) {
            $query->whereDate('last_login_at', $request->date);
        }

        $users = $query->with('roles')->paginate(30);

        $stats = [
            'today'        => User::whereDate('last_login_at', today())->count(),
            'this_week'    => User::where('last_login_at', '>=', now()->startOfWeek())->count(),
            'never_logged' => User::whereNull('last_login_at')->count(),
            'total_active' => User::where('is_active', true)->count(),
        ];

        return view('reports.login-activity', compact('users', 'stats'));
    }

    public function hostel(Request $request)
    {
        $hostels = Hostel::with('rooms')->get();

        // Overall stats
        $totalCapacity = HostelRoom::sum('capacity');
        $totalOccupied = RoomAllocation::where('status', 'active')->count();
        $totalRooms    = HostelRoom::count();
        $occupancyRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0;

        // Per-hostel breakdown
        $hostelStats = $hostels->map(function ($h) {
            $capacity  = $h->rooms->sum('capacity');
            $occupied  = RoomAllocation::whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $h->id))
                ->where('status', 'active')->count();
            return [
                'hostel'    => $h,
                'rooms'     => $h->rooms->count(),
                'capacity'  => $capacity,
                'occupied'  => $occupied,
                'available' => max(0, $capacity - $occupied),
                'rate'      => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0,
            ];
        });

        // Room type breakdown
        $roomsByType = HostelRoom::selectRaw('room_type, COUNT(*) as rooms, SUM(capacity) as capacity')->groupBy('room_type')->get();

        // Gender/type breakdown
        $byGender = $hostels->groupBy('type')->map(fn($g) => [
            'count'    => $g->count(),
            'capacity' => $g->sum(fn($h) => $h->rooms->sum('capacity')),
        ]);

        // Monthly allocation trend (last 12 months)
        $monthlyTrend = RoomAllocation::selectRaw("DATE_FORMAT(allocation_date, '%Y-%m') as month, COUNT(*) as count")
            ->where('allocation_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')->orderBy('month')->get();

        // Allocation list with filters
        $query = RoomAllocation::with(['student.user', 'hostelRoom.hostel']);
        if ($request->hostel_id)  $query->whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $request->hostel_id));
        if ($request->status)     $query->where('status', $request->status);
        if ($request->date_from)  $query->where('allocation_date', '>=', $request->date_from);
        if ($request->date_to)    $query->where('allocation_date', '<=', $request->date_to);

        $allocations = $query->latest()->paginate(25)->withQueryString();

        // Overdue checkouts
        $overdueCount = RoomAllocation::where('status', 'active')
            ->whereNotNull('expected_vacate_date')
            ->where('expected_vacate_date', '<', today())->count();

        return view('reports.hostel', compact(
            'hostels', 'totalCapacity', 'totalOccupied', 'totalRooms', 'occupancyRate',
            'hostelStats', 'roomsByType', 'byGender', 'monthlyTrend',
            'allocations', 'overdueCount'
        ));
    }

    public function admissions(Request $request)
    {
        $query = Admission::with(['program.department.faculty', 'semester.academicYear']);

        if ($request->academic_year_id) {
            $semIds = Semester::where('academic_year_id', $request->academic_year_id)->pluck('id');
            $query->whereIn('semester_id', $semIds);
        }
        if ($request->semester_id) $query->where('semester_id', $request->semester_id);
        if ($request->program_id)  $query->where('program_id', $request->program_id);
        if ($request->status)      $query->where('status', $request->status);
        if ($request->gender)      $query->where('gender', $request->gender);
        if ($request->from_date)   $query->whereDate('created_at', '>=', $request->from_date);
        if ($request->to_date)     $query->whereDate('created_at', '<=', $request->to_date);

        $admissions = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Summary stats (always all records, ignoring filters)
        $totalAll = Admission::count();
        $byStatus = Admission::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');
        $byGender = Admission::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')->pluck('count', 'gender');
        $thisYear = Admission::whereYear('created_at', now()->year)->count();
        $lastYear = Admission::whereYear('created_at', now()->year - 1)->count();

        $decided        = ($byStatus['approved'] ?? 0) + ($byStatus['rejected'] ?? 0);
        $acceptanceRate = $decided > 0 ? round(($byStatus['approved'] ?? 0) / $decided * 100, 1) : null;

        // Top programs by application volume
        $byProgram = Admission::selectRaw('program_id, COUNT(*) as count')
            ->with('program')
            ->groupBy('program_id')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Monthly trend — last 12 months (total + approved + rejected per month)
        $monthlyTrend = Admission::selectRaw(
                "DATE_FORMAT(created_at, '%Y-%m') as month,
                 COUNT(*) as total,
                 SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as approved,
                 SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) as rejected"
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesters     = Semester::orderBy('start_date', 'desc')->get();
        $programs      = Program::active()->orderBy('name')->get();

        return view('reports.admissions', compact(
            'admissions', 'totalAll', 'byStatus', 'byGender',
            'thisYear', 'lastYear', 'acceptanceRate',
            'byProgram', 'monthlyTrend',
            'academicYears', 'semesters', 'programs'
        ));
    }

    public function scholarships(Request $request)
    {
        $query = ScholarshipAward::with(['scholarship', 'student.user', 'student.program.department', 'awardedBy']);

        if ($request->scholarship_id) $query->where('scholarship_id', $request->scholarship_id);
        if ($request->status)         $query->where('scholarship_awards.status', $request->status);
        if ($request->from_date)      $query->whereDate('award_date', '>=', $request->from_date);
        if ($request->to_date)        $query->whereDate('award_date', '<=', $request->to_date);

        $awards = $query->orderByDesc('award_date')->paginate(30)->withQueryString();

        $allScholarships = Scholarship::orderBy('name')->get();

        // Stats on filtered set
        $base = ScholarshipAward::query();
        if ($request->scholarship_id) $base->where('scholarship_id', $request->scholarship_id);
        if ($request->status)         $base->where('scholarship_awards.status', $request->status);
        if ($request->from_date)      $base->whereDate('award_date', '>=', $request->from_date);
        if ($request->to_date)        $base->whereDate('award_date', '<=', $request->to_date);

        $totalAwards     = (clone $base)->count();
        $activeAwards    = (clone $base)->where('scholarship_awards.status', 'active')->count();
        $suspendedAwards = (clone $base)->where('scholarship_awards.status', 'suspended')->count();
        $uniqueStudents  = (clone $base)->distinct('student_id')->count('student_id');
        $byScholarship   = (clone $base)
            ->selectRaw('scholarship_id, COUNT(*) as cnt')
            ->groupBy('scholarship_id')
            ->get()
            ->mapWithKeys(fn($r) => [$r->scholarship_id => $r->cnt]);

        $stats = compact('totalAwards', 'activeAwards', 'suspendedAwards', 'uniqueStudents', 'byScholarship');

        return view('reports.scholarships', compact('awards', 'allScholarships', 'stats'));
    }

    public function export(Request $request, $type)
    {
        switch ($type) {
            case 'students':
                $sQuery = Student::with(['user', 'program.department.faculty']);
                $status = $request->input('status', 'active');
                if ($status && $status !== 'all') $sQuery->where('students.status', $status);
                if ($request->faculty_id)    $sQuery->byFaculty($request->faculty_id);
                if ($request->program_id)    $sQuery->where('program_id', $request->program_id);
                if ($request->gender)        $sQuery->where('gender', $request->gender);
                if ($request->admission_type)  $sQuery->where('admission_type', $request->admission_type);
                if ($request->year_of_study) $sQuery->where('year_of_study', $request->year_of_study);
                if ($request->enrollment_year) $sQuery->whereYear('enrollment_date', $request->enrollment_year);
                $data      = $sQuery->orderBy('program_id')->orderBy('id')->get();
                $byGender  = $data->groupBy('gender')->map->count();
                $byYear    = $data->groupBy('year_of_study')->map->count()->sortKeys();
                $byType    = $data->groupBy('admission_type')->map->count();
                $byProgram = $data->groupBy('program_id')->map(fn($g) => [
                    'program' => $g->first()->program,
                    'count'   => $g->count(),
                    'male'    => $g->where('gender', 'male')->count(),
                    'female'  => $g->where('gender', 'female')->count(),
                ])->sortByDesc('count')->values();
                // Human-readable filter labels for PDF header
                $filterLabels = array_filter([
                    $request->faculty_id    ? 'Faculty: ' . (Faculty::find($request->faculty_id)?->name ?? $request->faculty_id) : null,
                    $request->program_id    ? 'Program: ' . (Program::find($request->program_id)?->name ?? $request->program_id) : null,
                    $request->gender        ? 'Gender: ' . ucfirst($request->gender) : null,
                    $request->admission_type  ? 'Type: ' . ucfirst(str_replace('-', ' ', $request->admission_type)) : null,
                    $request->year_of_study ? 'Year: Year ' . $request->year_of_study : null,
                    $request->enrollment_year ? 'Enrolled: ' . $request->enrollment_year : null,
                    $status !== 'all'       ? 'Status: ' . ucfirst($status) : null,
                ]);
                $totalDropouts = \App\Models\Student::where('status', 'dropped_out')->count();
                $pdf = Pdf::loadView('reports.exports.students', compact('data', 'byGender', 'byYear', 'byType', 'byProgram', 'filterLabels', 'status', 'totalDropouts'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download('students_by_program_' . date('Ymd') . '.pdf');
            case 'finance':
                $data = Payment::with('studentBill.student.user')->where('status', 'verified')->whereYear('payment_date', date('Y'))->get();
                $pdf = Pdf::loadView('reports.exports.finance', compact('data'))->setPaper('a4', 'landscape');
                return $pdf->download('finance_report_' . date('Ymd') . '.pdf');
            case 'hostel':
                $hostels = Hostel::with('rooms')->get();
                $hostelStats = $hostels->map(function ($h) {
                    $capacity = $h->rooms->sum('capacity');
                    $occupied = RoomAllocation::whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $h->id))->where('status', 'active')->count();
                    return ['hostel' => $h, 'rooms' => $h->rooms->count(), 'capacity' => $capacity, 'occupied' => $occupied, 'available' => max(0, $capacity - $occupied), 'rate' => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0];
                });
                $query = RoomAllocation::with(['student.user', 'hostelRoom.hostel']);
                if ($request->hostel_id) $query->whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $request->hostel_id));
                if ($request->status)    $query->where('status', $request->status);
                if ($request->date_from) $query->where('allocation_date', '>=', $request->date_from);
                if ($request->date_to)   $query->where('allocation_date', '<=', $request->date_to);
                $allocations = $query->latest()->get();
                $totalCapacity = HostelRoom::sum('capacity');
                $totalOccupied = RoomAllocation::where('status', 'active')->count();
                $occupancyRate = $totalCapacity > 0 ? round(($totalOccupied / $totalCapacity) * 100, 1) : 0;
                $pdf = Pdf::loadView('reports.exports.hostel', compact('hostelStats', 'allocations', 'totalCapacity', 'totalOccupied', 'occupancyRate'))->setPaper('a4', 'landscape');
                return $pdf->download('hostel_report_' . date('Ymd') . '.pdf');
            case 'admissions':
                $aQuery = Admission::with(['program.department', 'reviewer', 'semester.academicYear']);
                if ($request->academic_year_id) {
                    $semIds = Semester::where('academic_year_id', $request->academic_year_id)->pluck('id');
                    $aQuery->whereIn('semester_id', $semIds);
                }
                if ($request->semester_id) $aQuery->where('semester_id', $request->semester_id);
                if ($request->program_id)  $aQuery->where('program_id', $request->program_id);
                if ($request->status)      $aQuery->where('status', $request->status);
                if ($request->gender)      $aQuery->where('gender', $request->gender);
                if ($request->from_date)   $aQuery->whereDate('created_at', '>=', $request->from_date);
                if ($request->to_date)     $aQuery->whereDate('created_at', '<=', $request->to_date);
                $admissions     = $aQuery->orderByDesc('created_at')->get();
                $byStatus       = Admission::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');
                $totalAll       = Admission::count();
                $decided        = ($byStatus['approved'] ?? 0) + ($byStatus['rejected'] ?? 0);
                $acceptanceRate = $decided > 0 ? round(($byStatus['approved'] ?? 0) / $decided * 100, 1) : null;
                $pdf = Pdf::loadView('reports.exports.admissions', compact('admissions', 'byStatus', 'totalAll', 'acceptanceRate'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download('admissions_report_' . date('Ymd') . '.pdf');

            case 'scholarships':
                $saQuery = ScholarshipAward::with(['scholarship', 'student.user', 'student.program.department', 'awardedBy']);
                if ($request->scholarship_id) $saQuery->where('scholarship_id', $request->scholarship_id);
                if ($request->status)         $saQuery->where('scholarship_awards.status', $request->status);
                if ($request->from_date)      $saQuery->whereDate('award_date', '>=', $request->from_date);
                if ($request->to_date)        $saQuery->whereDate('award_date', '<=', $request->to_date);
                $data            = $saQuery->orderByDesc('award_date')->get();
                $totalAwards     = $data->count();
                $activeAwards    = $data->where('status', 'active')->count();
                $uniqueStudents  = $data->unique('student_id')->count();
                $byScholarship   = $data->groupBy('scholarship_id')->map(fn($g) => [
                    'scholarship' => $g->first()->scholarship,
                    'count'       => $g->count(),
                    'active'      => $g->where('status', 'active')->count(),
                ]);
                $filterLabels = array_filter([
                    $request->scholarship_id ? 'Scholarship: ' . (Scholarship::find($request->scholarship_id)?->name ?? $request->scholarship_id) : null,
                    $request->status         ? 'Status: ' . ucfirst($request->status) : null,
                    $request->from_date      ? 'From: ' . $request->from_date : null,
                    $request->to_date        ? 'To: ' . $request->to_date : null,
                ]);
                $pdf = Pdf::loadView('reports.exports.scholarships', compact('data', 'totalAwards', 'activeAwards', 'uniqueStudents', 'byScholarship', 'filterLabels'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download('scholarship_awards_' . date('Ymd') . '.pdf');

            default:
                return back()->with('error', 'Invalid export type.');
        }
    }
}
