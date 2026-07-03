<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Staff;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use App\Models\Payment;
use App\Models\StudentBill;
use App\Models\Announcement;
use App\Models\CourseOffering;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Admission;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\LibraryBook;
use App\Models\Employee;
use App\Models\EmployeeAppointment;
use App\Models\EmploymentListing;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\SalaryAdvance;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\RoomAllocation;
use App\Models\SupportTicket;
use App\Models\FinalResult;
use App\Models\Timetable;
use App\Models\Examination;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            return $this->studentDashboard();
        }

        if ($user->hasRole('registrar')) {
            return $this->registrarDashboard();
        }

        if ($user->hasRole('lecturer')) {
            return $this->lecturerDashboard();
        }

        if ($user->hasRole('hostel-manager')) {
            return $this->hostelDashboard();
        }

        if ($user->hasRole('hr-officer')) {
            return $this->hrDashboard();
        }

        if ($user->hasRole('librarian')) {
            return $this->libraryDashboard();
        }

        $data = [
            'totalStudents' => Student::where('status', 'active')->count(),
            'totalStaff' => Staff::count(),
            'totalFaculties' => Faculty::count(),
            'totalDepartments' => Department::count(),
            'totalPrograms' => Program::count(),
            'totalCourses' => Course::count(),
            'recentStudents' => Student::with(['program.department', 'user'])->latest()->take(5)->get(),
            'recentAdmissions' => Admission::with('program')->latest()->take(5)->get(),
            'announcements' => Announcement::where('is_published', true)->latest()->take(5)->get(),
            'currentAcademicYear' => AcademicYear::where('is_current', true)->first(),
            'currentSemester' => Semester::where('is_current', true)->first(),
        ];

        if ($user->hasRole('super-admin') || $user->hasRole('finance-officer')) {
            $data['totalRevenue'] = Payment::where('status', 'completed')
                ->whereYear('created_at', date('Y'))->sum('amount');
            $data['outstandingBalance'] = StudentBill::sum('balance');
            $data['recentPayments'] = Payment::with('studentBill.student.user')->latest()->take(5)->get();
            $data['monthlyRevenue'] = Payment::where('status', 'completed')
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')->get();
        }

        if ($user->hasRole('student')) {
            $student = Student::where('user_id', $user->id)->first();
            $data['myStudent'] = $student;
            $data['myBill'] = StudentBill::where('student_id', $student?->id)
                ->where('status', '!=', 'paid')->first();
            $data['myCourses'] = CourseOffering::whereHas('registrations', function ($q) use ($student) {
                $q->where('student_id', $student?->id)->where('status', 'approved');
            })->with('course', 'lecturer.user')->get();
        }

        if ($user->hasRole('lecturer')) {
            $staff = Staff::where('user_id', $user->id)->first();
            $data['myOfferings'] = CourseOffering::where('lecturer_id', $staff?->id)
                ->with('course', 'semester')->get();
        }

        $data['enrollmentTrend'] = Student::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy('year')->orderBy('year')->take(5)->get();

        $data['openTickets'] = SupportTicket::where('status', 'open')->count();

        return view('dashboard.index', $data);
    }

    private function studentDashboard()
    {
        $user    = auth()->user();
        $student = Student::where('user_id', $user->id)
            ->with(['program.department', 'gpaRecords' => fn($q) => $q->latest()->limit(2)])
            ->first();

        $currentSemester    = Semester::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Registered course offerings this semester
        $myCourses = CourseOffering::whereHas('registrations', function ($q) use ($student) {
                $q->where('student_id', $student?->id)->whereIn('status', ['approved', 'registered']);
            })
            ->when($currentSemester, fn($q) => $q->where('semester_id', $currentSemester->id))
            ->with(['course', 'lecturer.user'])
            ->get();

        $offeringIds = $myCourses->pluck('id');

        // GPA / CGPA from GpaRecord
        $latestGpaRecord = $student?->gpaRecords()
            ->when($currentSemester, fn($q) => $q->where('semester_id', $currentSemester->id))
            ->latest()->first()
            ?? $student?->gpaRecords()->latest()->first();

        // Attendance rate across registered offerings
        $totalSessions = AttendanceSession::whereIn('course_offering_id', $offeringIds)->count();
        $presentCount  = AttendanceRecord::where('student_id', $student?->id)
            ->where('status', 'present')
            ->whereHas('session', fn($q) => $q->whereIn('course_offering_id', $offeringIds))
            ->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100) : null;

        // Outstanding balance
        $outstandingBalance = StudentBill::where('student_id', $student?->id)
            ->where('balance', '>', 0)->sum('balance');

        // Upcoming exams (next 14 days) for registered courses
        $upcomingExams = Examination::whereIn('course_offering_id', $offeringIds)
            ->where('exam_date', '>=', today())
            ->where('exam_date', '<=', today()->addDays(14))
            ->with(['courseOffering.course'])
            ->orderBy('exam_date')
            ->get();

        // Recent results
        $recentResults = FinalResult::where('student_id', $student?->id)
            ->with(['courseOffering.course', 'courseOffering.semester'])
            ->latest()->take(6)->get();

        // Announcements
        $announcements = Announcement::where('is_published', true)->latest()->take(5)->get();

        return view('dashboard.student', compact(
            'student', 'currentSemester', 'currentAcademicYear',
            'myCourses', 'latestGpaRecord',
            'attendanceRate', 'presentCount', 'totalSessions',
            'outstandingBalance', 'upcomingExams', 'recentResults', 'announcements'
        ));
    }

    private function registrarDashboard()
    {
        $currentSemester     = Semester::where('is_current', true)->first();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // Student stats
        $totalActiveStudents   = Student::where('status', 'active')->count();
        $pendingAdmissions     = Admission::where('status', 'pending')->count();
        $newAdmissionsThisYear = Admission::whereYear('created_at', now()->year)->count();

        // Registrations for current semester
        $semId = $currentSemester?->id;
        $activeRegistrations = CourseRegistration::where('status', 'registered')
            ->when($semId, fn($q) => $q->whereHas('courseOffering', fn($r) => $r->where('semester_id', $semId)))
            ->count();

        // Course offerings this semester
        $totalOfferings = CourseOffering::when($semId, fn($q) => $q->where('semester_id', $semId))->count();

        // Registrations by status (current semester)
        $regsByStatus = CourseRegistration::when($semId, fn($q) =>
                $q->whereHas('courseOffering', fn($r) => $r->where('semester_id', $semId))
            )
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Upcoming exams in next 14 days
        $upcomingExams = Examination::where('exam_date', '>=', today())
            ->where('exam_date', '<=', today()->addDays(14))
            ->when($semId, fn($q) => $q->whereHas('courseOffering', fn($r) => $r->where('semester_id', $semId)))
            ->with(['courseOffering.course'])
            ->orderBy('exam_date')
            ->get();

        // Offerings with enrolled students but no results yet (pending result entry)
        $offeringsWithNoResults = CourseOffering::when($semId, fn($q) => $q->where('semester_id', $semId))
            ->whereHas('registrations', fn($q) => $q->where('status', 'registered'))
            ->whereDoesntHave('finalResults')
            ->with('course')
            ->orderBy('created_at')
            ->take(8)
            ->get();

        // Program enrollment summary
        $programStats = Program::withCount(['students as active_students' => fn($q) => $q->where('status', 'active')])
            ->having('active_students', '>', 0)
            ->orderByDesc('active_students')
            ->take(8)
            ->get();

        // Recent admissions
        $recentAdmissions = Admission::with('program')->latest()->take(6)->get();

        // Recent course registrations (current semester)
        $recentRegistrations = CourseRegistration::with(['student.user', 'courseOffering.course'])
            ->when($semId, fn($q) => $q->whereHas('courseOffering', fn($r) => $r->where('semester_id', $semId)))
            ->whereIn('status', ['registered', 'approved'])
            ->latest()
            ->take(6)
            ->get();

        // Announcements
        $announcements = Announcement::where('is_published', true)->latest()->take(5)->get();

        return view('dashboard.registrar', compact(
            'currentSemester', 'currentAcademicYear',
            'totalActiveStudents', 'pendingAdmissions', 'newAdmissionsThisYear',
            'activeRegistrations', 'totalOfferings',
            'regsByStatus', 'upcomingExams',
            'offeringsWithNoResults',
            'programStats', 'recentAdmissions', 'recentRegistrations', 'announcements'
        ));
    }

    private function lecturerDashboard()
    {
        $staff = Staff::where('user_id', auth()->id())->with('department')->first();

        $currentSemester = Semester::where('is_current', true)->first();

        // Course offerings assigned to this lecturer (current semester)
        $myOfferings = CourseOffering::where('lecturer_id', $staff?->id)
            ->when($currentSemester, fn($q) => $q->where('semester_id', $currentSemester->id))
            ->with(['course', 'semester', 'timetables'])
            ->withCount(['registrations as enrolled' => fn($q) => $q->where('status', 'registered')])
            ->get();

        $totalStudents   = $myOfferings->sum('enrolled');
        $totalCourses    = $myOfferings->count();

        // Today's timetable entries
        $todayName   = strtolower(now()->format('l'));
        $offeringIds = $myOfferings->pluck('id');

        $todayClasses = Timetable::whereIn('course_offering_id', $offeringIds)
            ->where('day_of_week', $todayName)
            ->with('courseOffering.course')
            ->orderBy('start_time')
            ->get();

        // Upcoming exams in next 14 days
        $upcomingExams = Examination::whereIn('course_offering_id', $offeringIds)
            ->whereBetween('exam_date', [today(), today()->addDays(14)])
            ->with('courseOffering.course')
            ->orderBy('exam_date')
            ->get();

        // Attendance sessions taken this month
        $attendanceThisMonth = AttendanceSession::whereIn('course_offering_id', $offeringIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        // Recent attendance sessions
        $recentSessions = AttendanceSession::whereIn('course_offering_id', $offeringIds)
            ->with('courseOffering.course')
            ->latest('date')
            ->take(6)
            ->get();

        // Courses with pending results (active offerings with enrolled students but no final results)
        $pendingResults = $myOfferings->filter(function ($offering) {
            return $offering->enrolled > 0 &&
                   \App\Models\FinalResult::where('course_offering_id', $offering->id)->count() === 0;
        });

        // Recent announcements
        $announcements = Announcement::where('is_published', true)->latest()->take(5)->get();

        return view('dashboard.lecturer', compact(
            'staff', 'currentSemester', 'myOfferings',
            'totalStudents', 'totalCourses',
            'todayClasses', 'upcomingExams',
            'attendanceThisMonth', 'recentSessions',
            'pendingResults', 'announcements'
        ));
    }

    private function libraryDashboard()
    {
        $totalTitles     = LibraryBook::count();
        $totalCopies     = LibraryBook::sum('copies_total');
        $availableCopies = LibraryBook::sum('copies_available');
        $borrowed        = BookBorrowing::where('status', 'borrowed')->count();
        $overdue         = BookBorrowing::where('status', 'borrowed')->where('due_date', '<', today())->count();
        $unpaidFines     = BookBorrowing::where('fine_amount', '>', 0)->where('fine_paid', false)->sum('fine_amount');

        // Books by category
        $byCategory = BookCategory::withCount('books')
            ->having('books_count', '>', 0)
            ->orderByDesc('books_count')
            ->get();

        // Most borrowed books (all time top 5)
        $mostBorrowed = LibraryBook::withCount('borrowings')
            ->orderByDesc('borrowings_count')
            ->take(5)->get();

        // Overdue borrowings
        $overdueBorrowings = BookBorrowing::with(['book', 'student.user', 'user'])
            ->where('status', 'borrowed')
            ->where('due_date', '<', today())
            ->orderBy('due_date')
            ->take(10)->get();

        // Recent borrowings (last 8 issued)
        $recentBorrowings = BookBorrowing::with(['book', 'student.user', 'user'])
            ->latest('issue_date')
            ->take(8)->get();

        // Monthly borrowing trend (last 6 months)
        $monthlyTrend = BookBorrowing::selectRaw("DATE_FORMAT(issue_date, '%Y-%m') as month, COUNT(*) as issued, SUM(CASE WHEN status='returned' THEN 1 ELSE 0 END) as returned")
            ->where('issue_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.library', compact(
            'totalTitles', 'totalCopies', 'availableCopies', 'borrowed', 'overdue', 'unpaidFines',
            'byCategory', 'mostBorrowed', 'overdueBorrowings', 'recentBorrowings', 'monthlyTrend'
        ));
    }

    private function hrDashboard()
    {
        $totalEmployees   = Employee::active()->count();
        $pendingLeaves    = LeaveRequest::where('status', 'pending')->count();
        $openListings     = EmploymentListing::where('status', 'active')->count();
        $pendingAdvances  = SalaryAdvance::where('status', 'pending')->count();

        $currentMonth = now()->month;
        $currentYear  = now()->year;
        $payrollProcessed = Payroll::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->count();
        $payrollPending   = Payroll::where('month', $currentMonth)->where('year', $currentYear)->where('status', 'pending')->count();
        $payrollNetTotal  = Payroll::where('month', $currentMonth)->where('year', $currentYear)->sum('net_pay');

        // Employees by department
        $byDepartment = Department::withCount(['employees as active_count' => fn($q) => $q->where('status', 'active')])
            ->having('active_count', '>', 0)
            ->orderByDesc('active_count')
            ->get();

        // Employees by employment type
        $byType = Employee::active()
            ->selectRaw('employment_type, COUNT(*) as count')
            ->groupBy('employment_type')
            ->get();

        // Pending leave requests (latest 8)
        $recentLeaves = LeaveRequest::with(['employee.user', 'leaveType'])
            ->where('status', 'pending')
            ->latest()->take(8)->get();

        // Contracts expiring in the next 30 days
        $expiringContracts = Employee::with(['user', 'department'])
            ->where('status', 'active')
            ->whereNotNull('contract_end_date')
            ->whereBetween('contract_end_date', [today(), today()->addDays(30)])
            ->orderBy('contract_end_date')
            ->get();

        // Recently joined employees (last 30 days)
        $recentHires = Employee::with(['user', 'department'])
            ->where('join_date', '>=', today()->subDays(30))
            ->orderByDesc('join_date')
            ->take(5)->get();

        // Monthly payroll trend (last 6 months)
        $payrollTrend = Payroll::selectRaw('month, year, SUM(net_pay) as total, COUNT(*) as count')
            ->where(function ($q) {
                $q->where('year', now()->year)
                  ->where('month', '>=', now()->subMonths(5)->month)
                  ->orWhere(function ($q2) {
                      $q2->where('year', now()->subMonths(5)->year)
                         ->where('month', '>=', now()->subMonths(5)->month);
                  });
            })
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get();

        return view('dashboard.hr', compact(
            'totalEmployees', 'pendingLeaves', 'openListings', 'pendingAdvances',
            'payrollProcessed', 'payrollPending', 'payrollNetTotal',
            'byDepartment', 'byType',
            'recentLeaves', 'expiringContracts', 'recentHires', 'payrollTrend'
        ));
    }

    private function hostelDashboard()
    {
        $hostels = Hostel::with(['rooms'])->get();

        $totalRooms     = HostelRoom::count();
        $totalCapacity  = HostelRoom::sum('capacity');
        $occupied       = RoomAllocation::where('status', 'active')->count();
        $availableBeds  = max(0, $totalCapacity - $occupied);
        $occupancyRate  = $totalCapacity > 0 ? round(($occupied / $totalCapacity) * 100, 1) : 0;

        // Per-hostel breakdown
        $hostelStats = $hostels->map(function ($hostel) {
            $capacity  = $hostel->rooms->sum('capacity');
            $occupants = RoomAllocation::whereHas('hostelRoom', fn($q) => $q->where('hostel_id', $hostel->id))
                ->where('status', 'active')->count();
            return [
                'hostel'    => $hostel,
                'capacity'  => $capacity,
                'occupants' => $occupants,
                'available' => max(0, $capacity - $occupants),
                'rate'      => $capacity > 0 ? round(($occupants / $capacity) * 100) : 0,
            ];
        });

        // Room type counts
        $roomsByType = HostelRoom::selectRaw('room_type, COUNT(*) as count, SUM(capacity) as capacity')
            ->groupBy('room_type')->get();

        // Recent allocations
        $recentAllocations = RoomAllocation::with(['student.user', 'hostelRoom.hostel'])
            ->where('status', 'active')
            ->latest()->take(8)->get();

        // Upcoming checkouts (expected vacate within next 7 days)
        $upcomingCheckouts = RoomAllocation::with(['student.user', 'hostelRoom.hostel'])
            ->where('status', 'active')
            ->whereNotNull('expected_vacate_date')
            ->whereBetween('expected_vacate_date', [today(), today()->addDays(7)])
            ->orderBy('expected_vacate_date')
            ->get();

        // Overdue checkouts
        $overdueCheckouts = RoomAllocation::with(['student.user', 'hostelRoom.hostel'])
            ->where('status', 'active')
            ->whereNotNull('expected_vacate_date')
            ->where('expected_vacate_date', '<', today())
            ->orderBy('expected_vacate_date')
            ->get();

        return view('dashboard.hostel', compact(
            'hostels', 'totalRooms', 'totalCapacity', 'occupied',
            'availableBeds', 'occupancyRate', 'hostelStats', 'roomsByType',
            'recentAllocations', 'upcomingCheckouts', 'overdueCheckouts'
        ));
    }

    public function stats()
    {
        return response()->json([
            'students' => Student::where('status', 'active')->count(),
            'staff' => Staff::count(),
            'revenue' => Payment::where('status', 'completed')->whereMonth('created_at', date('m'))->sum('amount'),
            'tickets' => SupportTicket::where('status', 'open')->count(),
        ]);
    }
}
