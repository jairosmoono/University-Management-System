<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private string $settingsPath = 'settings.json';

    private function loadSettings(): array
    {
        if (Storage::exists($this->settingsPath)) {
            return json_decode(Storage::get($this->settingsPath), true) ?? [];
        }
        return [];
    }

    private function saveSettings(array $data): void
    {
        $current = $this->loadSettings();
        Storage::put($this->settingsPath, json_encode(array_merge($current, $data), JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $auditLogs           = AuditLog::with('user')->latest('created_at')->paginate(20);
        $academicYears       = AcademicYear::with('semesters')->orderBy('start_date', 'desc')->get();
        $currentAcademicYear = AcademicYear::current();
        $currentSemester     = Semester::current();
        $settings            = $this->loadSettings();
        $courseTypes         = $settings['course_types'] ?? ['core', 'elective', 'lab'];
        return view('admin.settings.index', compact('auditLogs', 'academicYears', 'currentAcademicYear', 'currentSemester', 'settings', 'courseTypes'));
    }

    public function storeCourseType(Request $request)
    {
        $request->validate(['course_type' => 'required|string|max:50|regex:/^[a-z0-9_\- ]+$/i']);
        $settings = $this->loadSettings();
        $types    = $settings['course_types'] ?? ['core', 'elective', 'lab'];
        $newType  = strtolower(trim($request->course_type));
        if (!in_array($newType, $types)) {
            $types[] = $newType;
            $this->saveSettings(['course_types' => $types]);
        }
        return back()->with('success', 'Course type added.');
    }

    public function destroyCourseType(string $type)
    {
        $settings = $this->loadSettings();
        $types    = $settings['course_types'] ?? ['core', 'elective', 'lab'];
        $types    = array_values(array_filter($types, fn($t) => $t !== $type));
        $this->saveSettings(['course_types' => $types]);
        return back()->with('success', 'Course type removed.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'university_name'  => 'required|string|max:255',
            'university_email' => 'nullable|email',
            'university_phone' => 'nullable|string|max:20',
        ]);

        $data = $request->except('_token', '_method', 'group');
        $data['maintenance_mode']  = $request->boolean('maintenance_mode');
        $data['registration_open'] = $request->boolean('registration_open');
        $data['admissions_open']   = $request->boolean('admissions_open');

        $this->saveSettings($data);

        return back()->with('success', 'Settings saved successfully.');
    }

    public function uploadBranding(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:png,ico,svg|max:512',
        ]);

        $settings = $this->loadSettings();

        if ($request->hasFile('logo')) {
            if (!empty($settings['logo_path'])) {
                Storage::disk('public')->delete($settings['logo_path']);
            }
            $path = $request->file('logo')->storeAs('branding', 'logo.' . $request->file('logo')->getClientOriginalExtension(), 'public');
            $settings['logo_path'] = $path;
        }

        if ($request->hasFile('favicon')) {
            if (!empty($settings['favicon_path'])) {
                Storage::disk('public')->delete($settings['favicon_path']);
            }
            $path = $request->file('favicon')->storeAs('branding', 'favicon.' . $request->file('favicon')->getClientOriginalExtension(), 'public');
            $settings['favicon_path'] = $path;
        }

        Storage::put($this->settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

        return back()->with('success', 'Branding updated successfully.');
    }

    public function uploadHeroImages(Request $request)
    {
        $request->validate([
            'hero_images'   => 'required|array|min:1',
            'hero_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $settings = $this->loadSettings();
        $paths    = $settings['hero_images'] ?? [];

        foreach ($request->file('hero_images') as $file) {
            $paths[] = $file->store('hero', 'public');
        }

        $settings['hero_images'] = $paths;
        Storage::put($this->settingsPath, json_encode($settings, JSON_PRETTY_PRINT));

        return back()->with('success', count($request->file('hero_images')) . ' hero image(s) uploaded.');
    }

    public function deleteHeroImage(Request $request)
    {
        $request->validate(['path' => 'required|string']);

        $settings = $this->loadSettings();
        $paths    = $settings['hero_images'] ?? [];

        if (in_array($request->path, $paths)) {
            Storage::disk('public')->delete($request->path);
            $settings['hero_images'] = array_values(array_filter($paths, fn($p) => $p !== $request->path));
            Storage::put($this->settingsPath, json_encode($settings, JSON_PRETTY_PRINT));
        }

        return back()->with('success', 'Hero image removed.');
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->action)  $query->where('action', $request->action);
        if ($request->date)    $query->whereDate('created_at', $request->date);
        $logs = $query->paginate(25);
        return view('admin.audit-logs.index', compact('logs'));
    }
}
