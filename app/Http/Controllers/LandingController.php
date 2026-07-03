<?php
namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Course;
use App\Models\Department;
use App\Models\Announcement;
use App\Models\EmploymentListing;

class LandingController extends Controller
{
    private function loadSettings(): array
    {
        $path = storage_path('app/settings.json');
        return file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
    }

    public function index()
    {
        $uni = $this->loadSettings();

        $stats = [
            'students'    => Student::count(),
            'programs'    => Program::count(),
            'faculties'   => Faculty::count(),
            'departments' => Department::count(),
            'courses'     => Course::count(),
            'staff'       => Staff::count(),
        ];

        $faculties = Faculty::withCount('departments')
            ->with(['departments.programs' => fn($q) => $q->limit(3)])
            ->get();

        $programs = Program::with('department.faculty')
            ->active()
            ->orderByRaw("FIELD(level,'degree','diploma','certificate','craft_certificate','trade_test_certificate')")
            ->orderBy('name')
            ->take(9)
            ->get();

        $announcements = Announcement::active()
            ->whereIn('category', ['general', 'event', 'events'])
            ->latest()
            ->take(3)
            ->get();

        $heroImages = collect($uni['hero_images'] ?? [])->map(fn($p) => asset('storage/' . $p))->values()->all();

        $featuredJobs = EmploymentListing::with('department')
            ->where('status', 'open')
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', now()))
            ->latest()
            ->take(3)
            ->get();

        return view('landing', compact('uni', 'stats', 'faculties', 'programs', 'announcements', 'heroImages', 'featuredJobs'));
    }

    public function programs()
    {
        $uni = $this->loadSettings();

        $programs = Program::with('department.faculty')
            ->active()
            ->orderByRaw("FIELD(level,'degree','diploma','certificate','craft_certificate','trade_test_certificate')")
            ->orderBy('name')
            ->get();

        $levelLabels = \App\Models\Program::levelLabels();

        $grouped = $programs->groupBy('level');

        return view('programs', compact('uni', 'programs', 'grouped', 'levelLabels'));
    }

    public function jobs()
    {
        $uni = $this->loadSettings();

        $jobs = EmploymentListing::with('department')
            ->where('status', 'open')
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>=', now()))
            ->latest()
            ->paginate(12);

        $departments = Department::orderBy('name')->get();

        return view('jobs-public', compact('uni', 'jobs', 'departments'));
    }

    public function announcements()
    {
        $uni = $this->loadSettings();

        $announcements = Announcement::active()
            ->whereIn('category', ['general', 'event', 'events'])
            ->latest()
            ->paginate(12);

        return view('announcements-public', compact('uni', 'announcements'));
    }
}
