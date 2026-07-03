<?php
namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Program;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdmissionController extends Controller
{
    private function admissionsOpen(): bool
    {
        $path = storage_path('app/settings.json');
        $settings = file_exists($path) ? (json_decode(file_get_contents($path), true) ?? []) : [];
        return $settings['admissions_open'] ?? true;
    }

    public function publicApply()
    {
        $settingsPath = storage_path('app/settings.json');
        $uni = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?? []) : [];

        if (!$this->admissionsOpen()) {
            return view('admissions.public_closed', compact('uni'));
        }

        $programs  = Program::with('department.faculty')->active()->orderBy('name')->get();
        $semesters = Semester::orderBy('name')->get();
        $faculties = Faculty::active()->orderBy('name')->get();

        return view('admissions.public_apply', compact('programs', 'semesters', 'faculties', 'uni'));
    }

    public function publicStore(Request $request)
    {
        if (!$this->admissionsOpen()) {
            return redirect()->route('apply')->with('error', 'Online admission applications are currently closed.');
        }

        $request->validate([
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'email'                   => 'required|email',
            'phone'                   => 'required|string|max:20',
            'date_of_birth'           => 'required|date',
            'gender'                  => 'required|in:male,female,other',
            'program_id'              => 'required|exists:programs,id',
            'semester_id'             => 'nullable|exists:semesters,id',
            'previous_school'         => 'required|string|max:255',
            'qualification_type'      => 'required|string|max:100',
            'year_completed'          => 'required|digits:4|integer|min:1990|max:'.date('Y'),
            'grade'                   => 'required|string|max:50',
            'documents.certificates'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.national_id'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.photo'         => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $documents = [];
        $uploadDir = public_path('uploads/admissions');
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        foreach (['certificates', 'national_id', 'photo'] as $key) {
            if ($request->hasFile("documents.{$key}") && $request->file("documents.{$key}")->isValid()) {
                $file = $request->file("documents.{$key}");
                $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $documents[$key] = 'uploads/admissions/' . $filename;
            }
        }

        $appNumber = generateApplicationNumber();

        Admission::create([
            'application_number' => $appNumber,
            'first_name'         => $request->first_name,
            'last_name'          => $request->last_name,
            'middle_name'        => $request->middle_name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'date_of_birth'      => $request->date_of_birth,
            'gender'             => $request->gender,
            'nationality'        => $request->nationality ?? 'Zambian',
            'address'            => $request->address,
            'program_id'         => $request->program_id,
            'semester_id'        => $request->semester_id,
            'previous_school'    => $request->previous_school,
            'qualification_type' => $request->qualification_type,
            'year_completed'     => $request->year_completed,
            'grade'              => $request->grade,
            'documents'          => $documents ?: null,
            'status'             => 'pending',
        ]);

        return redirect()->route('apply.success', ['ref' => $appNumber]);
    }

    public function publicSuccess(Request $request)
    {
        $ref = $request->query('ref');
        $settingsPath = storage_path('app/settings.json');
        $uni = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?? []) : [];
        return view('admissions.public_success', compact('ref', 'uni'));
    }

    public function index(Request $request)
    {
        $query = Admission::with(['program', 'semester']);
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->program_id) $query->where('program_id', $request->program_id);
        if ($request->semester_id) $query->where('semester_id', $request->semester_id);
        $admissions = $query->latest()->paginate(15);
        $programs = Program::active()->get();
        $stats = [
            'total'    => Admission::count(),
            'pending'  => Admission::where('status', 'pending')->count(),
            'approved' => Admission::where('status', 'approved')->count(),
            'rejected' => Admission::where('status', 'rejected')->count(),
        ];
        return view('admissions.index', compact('admissions', 'programs', 'stats'));
    }

    public function create()
    {
        $programs = Program::with('department.faculty')->active()->get();
        $semesters = Semester::orderBy('name')->get();
        $faculties = Faculty::active()->orderBy('name')->get();
        return view('admissions.create', compact('programs', 'semesters', 'faculties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'              => 'required|string|max:100',
            'last_name'               => 'required|string|max:100',
            'email'                   => 'required|email',
            'program_id'              => 'required|exists:programs,id',
            'semester_id'             => 'nullable|exists:semesters,id',
            'documents.certificates'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.national_id'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.photo'         => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'documents.other'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $documents = [];
        $uploadDir = public_path('uploads/admissions');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach (['certificates', 'national_id', 'photo', 'other'] as $key) {
            if ($request->hasFile("documents.{$key}") && $request->file("documents.{$key}")->isValid()) {
                $file     = $request->file("documents.{$key}");
                $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $documents[$key] = 'uploads/admissions/' . $filename;
            }
        }

        Admission::create([
            'application_number' => generateApplicationNumber(),
            'first_name'         => $request->first_name,
            'last_name'          => $request->last_name,
            'middle_name'        => $request->middle_name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'date_of_birth'      => $request->date_of_birth,
            'gender'             => $request->gender,
            'nationality'        => $request->nationality ?? 'Zambian',
            'address'            => $request->address,
            'program_id'         => $request->program_id,
            'semester_id'        => $request->semester_id,
            'previous_school'    => $request->previous_school,
            'qualification_type' => $request->qualification_type,
            'year_completed'     => $request->year_completed,
            'grade'              => $request->grade,
            'documents'          => $documents ?: null,
            'status'             => 'pending',
        ]);

        return redirect()->route('admissions.index')
            ->with('success', 'Application submitted successfully.');
    }

    public function show(Admission $admission)
    {
        $admission->load(['program', 'semester', 'reviewer']);
        return view('admissions.show', compact('admission'));
    }

    public function edit(Admission $admission)
    {
        $programs  = Program::active()->get();
        $semesters = Semester::orderBy('name')->get();
        return view('admissions.edit', compact('admission', 'programs', 'semesters'));
    }

    public function update(Request $request, Admission $admission)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'program_id' => 'required|exists:programs,id',
        ]);

        $admission->update($request->except('_token', '_method'));
        return redirect()->route('admissions.show', $admission)
            ->with('success', 'Application updated successfully.');
    }

    public function destroy(Admission $admission)
    {
        $admission->delete();
        return redirect()->route('admissions.index')
            ->with('success', 'Application deleted.');
    }

    public function approve(Request $request, Admission $admission)
    {
        $admission->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->notes,
        ]);
        return back()->with('success', 'Application approved. Send admission letter to applicant.');
    }

    public function reject(Request $request, Admission $admission)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $admission->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Application rejected.');
    }

    public function admissionLetter(Admission $admission)
    {
        if ($admission->status !== 'approved') {
            return back()->with('error', 'Only approved applications can generate admission letters.');
        }
        $pdf = Pdf::loadView('admissions.letter', compact('admission'))->setPaper('a4');
        $safeRef = str_replace(['/', '\\', ' '], '-', $admission->application_number);
        return $pdf->download("admission_letter_{$safeRef}.pdf");
    }
}
