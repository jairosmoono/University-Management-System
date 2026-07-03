<?php
namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }
        if ($request->faculty_id) {
            $query->whereHas('program.department', fn($q) => $q->where('faculty_id', $request->faculty_id));
        }
        if ($request->program_id) $query->where('program_id', $request->program_id);
        if ($request->status)     $query->where('status', $request->status);
        if ($request->sponsor)    $query->where('sponsor', 'like', '%' . $request->sponsor . '%');

        // Stats computed from the same filtered query (before pagination)
        $statsBase = clone $query;
        $total     = (clone $statsBase)->count();
        $byGender  = (clone $statsBase)
                        ->selectRaw("COALESCE(NULLIF(gender,''), 'unspecified') as gender, count(*) as total")
                        ->groupBy('gender')
                        ->pluck('total', 'gender');
        $bySponsor = (clone $statsBase)
                        ->selectRaw("COALESCE(NULLIF(sponsor,''), 'Unsponsored') as sponsor, count(*) as total")
                        ->groupBy('sponsor')
                        ->orderByDesc('total')
                        ->get();

        $students    = $query->with(['user', 'program.department.faculty'])->latest()->paginate(15);
        $faculties   = Faculty::active()->get();
        $departments = Department::active()->get();
        $programs    = Program::active()->get();

        return view('students.index',
            compact('students', 'faculties', 'departments', 'programs',
                    'total', 'byGender', 'bySponsor'));
    }

    public function create()
    {
        $faculties = Faculty::active()->get();
        $departments = Department::active()->get();
        $programs = Program::with('department.faculty')->active()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('students.create', compact('faculties', 'departments', 'programs', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'program_id'  => 'required|exists:programs,id',
            'gender'      => 'nullable|in:male,female,other',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'national_id' => 'nullable|string|max:50|unique:students,national_id',
        ]);

        DB::transaction(function () use ($request) {
            $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

            $user = User::create([
                'name'      => $fullName,
                'email'     => $request->email,
                'password'  => Hash::make($request->password ?? 'Student@123'),
                'phone'     => $request->phone,
                'is_active' => true,
            ]);
            $user->assignRole('student');

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

            $studentId = 'STU' . str_pad(Student::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT);

            $student = Student::create([
                'user_id'             => $user->id,
                'student_id'          => $studentId,
                'program_id'          => $request->program_id,
                'year_of_study'       => $request->year_of_study ?? 1,
                'admission_type'      => $request->admission_type ?? 'full-time',
                'gender'              => $request->gender,
                'date_of_birth'       => $request->date_of_birth,
                'nationality'         => $request->nationality ?? 'Zambian',
                'national_id'         => $request->national_id,
                'phone'               => $request->phone,
                'address'             => $request->address,
                'sponsor'             => $request->sponsor,
                'enrollment_date'     => $request->enrollment_date ?? now()->toDateString(),
                'expected_graduation' => $request->expected_graduation,
                'photo'               => $photoPath,
                'status'              => 'active',
            ]);

            if ($request->emergency_contact_name) {
                $student->guardians()->create([
                    'name'                 => $request->emergency_contact_name,
                    'relationship'         => 'guardian',
                    'phone'                => $request->emergency_contact_phone ?? '',
                    'is_emergency_contact' => true,
                ]);
            }
        });

        return redirect()->route('students.index')
            ->with('success', 'Student registered successfully.');
    }

    public function show(Student $student)
    {
        $student->load([
            'user', 'program.department.faculty', 'guardians',
            'courseRegistrations.courseOffering.course',
            'courseRegistrations.courseOffering.semester.academicYear',
            'finalResults.courseOffering.course',
            'finalResults.courseOffering.semester.academicYear',
            'bills',
            'gpaRecords.semester.academicYear',
            'hostelAllocation',
        ]);

        // Group results by semester for tabular display
        $resultsBySemester = $student->finalResults
            ->filter(fn($r) => $r->courseOffering)
            ->groupBy(fn($r) => $r->courseOffering->semester_id)
            ->sortByDesc(fn($g, $key) => optional($g->first()->courseOffering->semester)->start_date);

        // GPA records keyed by semester_id for quick lookup
        $gpaMap = $student->gpaRecords->keyBy('semester_id');

        // Registered course offerings available for adding a new result
        $registeredOfferings = $student->courseRegistrations
            ->map->courseOffering
            ->filter()
            ->unique('id')
            ->values();

        return view('students.show',
            compact('student', 'resultsBySemester', 'gpaMap', 'registeredOfferings'));
    }

    public function storeResult(Request $request, Student $student)
    {
        $request->validate([
            'course_offering_id' => 'required|exists:course_offerings,id',
            'ca_score'           => 'required|numeric|min:0|max:40',
            'exam_score'         => 'required|numeric|min:0|max:60',
        ]);

        $offering    = \App\Models\CourseOffering::with('semester')->findOrFail($request->course_offering_id);
        $total       = floatval($request->ca_score) + floatval($request->exam_score);
        $grade       = scoreToGrade($total);
        $gradePoints = gradeToPoints($grade);

        \App\Models\FinalResult::updateOrCreate(
            ['student_id' => $student->id, 'course_offering_id' => $offering->id],
            [
                'semester_id'      => $offering->semester_id,
                'academic_year_id' => $offering->semester?->academic_year_id,
                'ca_score'         => $request->ca_score,
                'exam_score'       => $request->exam_score,
                'total_score'      => $total,
                'grade'            => $grade,
                'grade_points'     => $gradePoints,
                'status'           => 'pending',
            ]
        );

        return back()->with('success', 'Result saved successfully.');
    }

    public function updateResult(Request $request, Student $student, \App\Models\FinalResult $result)
    {
        $request->validate([
            'ca_score'   => 'required|numeric|min:0|max:40',
            'exam_score' => 'required|numeric|min:0|max:60',
        ]);

        $total       = floatval($request->ca_score) + floatval($request->exam_score);
        $grade       = scoreToGrade($total);
        $gradePoints = gradeToPoints($grade);

        $result->update([
            'ca_score'     => $request->ca_score,
            'exam_score'   => $request->exam_score,
            'total_score'  => $total,
            'grade'        => $grade,
            'grade_points' => $gradePoints,
        ]);

        return back()->with('success', 'Result updated.');
    }

    public function approveResult(Student $student, \App\Models\FinalResult $result)
    {
        $result->load('courseOffering');
        $status = ($result->grade === 'F') ? 'fail' : 'pass';
        $result->update(['status' => $status]);

        app(\App\Http\Controllers\Academic\ResultController::class)
            ->recalculateGpa($student->id, $result->courseOffering->semester_id);

        return back()->with('success', 'Result approved and GPA updated.');
    }

    public function storeGuardian(Request $request, Student $student)
    {
        $request->validate([
            'name'                 => 'required|string|max:150',
            'relationship'         => 'required|string|max:50',
            'phone'                => 'required|string|max:30',
            'email'                => 'nullable|email|max:150',
            'address'              => 'nullable|string|max:500',
            'is_emergency_contact' => 'boolean',
        ]);

        $student->guardians()->create([
            'name'                 => $request->name,
            'relationship'         => $request->relationship,
            'phone'                => $request->phone,
            'email'                => $request->email,
            'address'              => $request->address,
            'is_emergency_contact' => $request->boolean('is_emergency_contact'),
        ]);

        return back()->with('success', 'Guardian added successfully.');
    }

    public function updateGuardian(Request $request, Student $student, \App\Models\StudentGuardian $guardian)
    {
        abort_if($guardian->student_id !== $student->id, 403);

        $request->validate([
            'name'                 => 'required|string|max:150',
            'relationship'         => 'required|string|max:50',
            'phone'                => 'required|string|max:30',
            'email'                => 'nullable|email|max:150',
            'address'              => 'nullable|string|max:500',
            'is_emergency_contact' => 'boolean',
        ]);

        $guardian->update([
            'name'                 => $request->name,
            'relationship'         => $request->relationship,
            'phone'                => $request->phone,
            'email'                => $request->email,
            'address'              => $request->address,
            'is_emergency_contact' => $request->boolean('is_emergency_contact'),
        ]);

        return back()->with('success', 'Guardian updated successfully.');
    }

    public function destroyGuardian(Student $student, \App\Models\StudentGuardian $guardian)
    {
        abort_if($guardian->student_id !== $student->id, 403);
        $guardian->delete();
        return back()->with('success', 'Guardian removed.');
    }

    public function edit(Student $student)
    {
        $faculties = Faculty::active()->get();
        $departments = Department::active()->get();
        $programs = Program::with('department.faculty')->active()->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        return view('students.edit', compact('student', 'faculties', 'departments', 'programs', 'academicYears'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name'  => 'required|string|max:100',
            'last_name'   => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email,' . $student->user_id,
            'program_id'  => 'required|exists:programs,id',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'national_id' => 'nullable|string|max:50|unique:students,national_id,' . $student->id,
        ]);

        DB::transaction(function () use ($request, $student) {
            $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

            $student->user->update([
                'name'  => $fullName,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $photoPath = $student->photo;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

            $student->update([
                'program_id'          => $request->program_id,
                'year_of_study'       => $request->year_of_study ?? $student->year_of_study,
                'admission_type'      => $request->admission_type ?? $student->admission_type,
                'gender'              => $request->gender,
                'date_of_birth'       => $request->date_of_birth,
                'nationality'         => $request->nationality,
                'national_id'         => $request->national_id,
                'phone'               => $request->phone,
                'address'             => $request->address,
                'sponsor'             => $request->sponsor,
                'enrollment_date'     => $request->enrollment_date ?? $student->enrollment_date,
                'expected_graduation' => $request->expected_graduation ?? $student->expected_graduation,
                'status'              => $request->status,
                'photo'               => $photoPath,
            ]);
        });

        return redirect()->route('students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $user = $student->user;

        // Delete photo from storage
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // Delete child rows that have no CASCADE, in dependency order
        DB::table('payments')
            ->whereIn('student_bill_id', DB::table('student_bills')->where('student_id', $student->id)->pluck('id'))
            ->delete();
        DB::table('grade_appeals')
            ->whereIn('final_result_id', DB::table('final_results')->where('student_id', $student->id)->pluck('id'))
            ->delete();
        DB::table('gpa_records')->where('student_id', $student->id)->delete();
        DB::table('student_holds')->where('student_id', $student->id)->delete();
        DB::table('course_waitlist')->where('student_id', $student->id)->delete();
        DB::table('course_registrations')->where('student_id', $student->id)->delete();
        DB::table('final_results')->where('student_id', $student->id)->delete();
        DB::table('room_allocations')->where('student_id', $student->id)->delete();
        DB::table('student_bills')->where('student_id', $student->id)->delete();

        $student->forceDelete();

        if ($user) {
            // RESTRICT FK: must delete before the user row
            DB::table('messages')->where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
            DB::table('announcements')->where('user_id', $user->id)->delete();
            $user->forceDelete();
        }

        return redirect()->route('students.index')
            ->with('success', 'Student permanently deleted.');
    }

    public function updateStatus(Request $request, Student $student)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended,graduated,dropped_out,deferred',
        ]);

        $old    = $student->status;
        $new    = $request->status;
        $labels = [
            'active'    => 'Reinstated',
            'inactive'  => 'Marked Inactive',
            'suspended' => 'Suspended',
            'graduated' => 'Marked as Graduated',
            'dropped_out' => 'Dropped Out',
            'deferred'  => 'Enrollment Deferred',
        ];

        $student->update(['status' => $new]);

        $msg = ($labels[$new] ?? ucfirst($new)) . ': ' . $student->full_name;

        return back()->with('success', $msg);
    }

    public function printCard(Student $student)
    {
        $student->load(['user', 'program.department.faculty', 'hostelAllocation']);

        $settingsRaw = Storage::exists('settings.json')
            ? json_decode(Storage::get('settings.json'), true) : [];
        $uniName  = $settingsRaw['university_name'] ?? 'University';
        $uniAddr  = $settingsRaw['university_address'] ?? '';
        $uniPhone = $settingsRaw['university_phone'] ?? '';
        $uniEmail = $settingsRaw['university_email'] ?? '';
        $logoSrc = null;
        if (!empty($settingsRaw['logo_path'])) {
            $lf = storage_path('app/public/' . $settingsRaw['logo_path']);
            if (file_exists($lf)) {
                $logoSrc = 'data:' . mime_content_type($lf) . ';base64,' . base64_encode(file_get_contents($lf));
            }
        }

        $pdf = Pdf::loadView('students.card', compact('student', 'logoSrc', 'uniName', 'uniAddr', 'uniPhone', 'uniEmail'))
            ->setPaper([0, 0, 242.56, 153.07])
            ->setOption(['margin_top' => 0, 'margin_bottom' => 0, 'margin_left' => 0, 'margin_right' => 0]);
        return $pdf->download("student_id_card_{$student->student_id}.pdf");
    }

    public function transcript(Student $student)
    {
        $student->load(['user', 'program.department', 'finalResults.courseOffering.course', 'finalResults.courseOffering.semester.academicYear', 'gpaRecords.semester']);
        $pdf = Pdf::loadView('students.transcript', compact('student'))->setPaper('a4');
        return $pdf->download("transcript_{$student->student_id}.pdf");
    }

    public function resultSlip(Student $student)
    {
        $semester = \App\Models\Semester::where('is_current', true)->first();
        $student->load(['user', 'program', 'finalResults' => function($q) use ($semester) {
            $q->whereHas('courseOffering', fn($co) => $co->where('semester_id', $semester?->id));
        }, 'finalResults.courseOffering.course']);
        $pdf = Pdf::loadView('students.result-slip', compact('student', 'semester'))->setPaper('a4');
        return $pdf->download("result_slip_{$student->student_id}.pdf");
    }

    public function bulkPrintCards(Request $request)
    {
        $request->validate([
            'student_ids'   => 'required|array|min:1|max:200',
            'student_ids.*' => 'exists:students,id',
        ]);

        $students = Student::with(['user', 'program.department.faculty', 'hostelAllocation'])
            ->whereIn('id', $request->student_ids)
            ->get();

        $settingsRaw = \Illuminate\Support\Facades\Storage::exists('settings.json')
            ? json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true)
            : [];

        $logoSrc = null;
        if (!empty($settingsRaw['logo_path'])) {
            $logoFile = storage_path('app/public/' . $settingsRaw['logo_path']);
            if (file_exists($logoFile)) {
                $mime    = mime_content_type($logoFile);
                $logoSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoFile));
            }
        }

        $uniName  = $settingsRaw['university_name'] ?? 'University';
        $uniAddr  = $settingsRaw['university_address'] ?? '';
        $uniPhone = $settingsRaw['university_phone'] ?? '';
        $uniEmail = $settingsRaw['university_email'] ?? '';

        $pdf = Pdf::loadView('students.cards-bulk', compact('students', 'logoSrc', 'uniName', 'uniAddr', 'uniPhone', 'uniEmail'))
            ->setPaper('a4')
            ->setOption(['margin_top' => 0, 'margin_bottom' => 0, 'margin_left' => 0, 'margin_right' => 0]);

        return $pdf->download('student_id_cards.pdf');
    }

    public function importTemplate()
    {
        $headers = [
            // Required
            'first_name', 'last_name', 'email', 'date_of_birth', 'gender', 'program_code',
            // Optional – personal
            'middle_name', 'phone', 'national_id', 'nationality', 'address', 'sponsor',
            // Optional – academic
            'year_of_study', 'enrollment_date', 'admission_type',
            // Optional – account & emergency contact
            'password', 'emergency_contact_name', 'emergency_contact_phone',
        ];
        $example = [
            'Jane', 'Banda', 'jane.banda@example.com', '2000-05-14', 'female', 'BSCS',
            'Mary', '+260971000001', '123456/78/1', 'Zambian', 'Plot 12, Lusaka', 'Self',
            '1', date('Y-m-d'), 'full-time',
            'Student@123', 'John Banda', '+260977000002',
        ];

        // Use fputcsv so values with commas (e.g. addresses) are properly quoted
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        fputcsv($handle, $example);
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
        ]);
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls,xml,txt|max:10240',
        ]);

        set_time_limit(300);

        $ext  = strtolower($request->file('file')->getClientOriginalExtension());
        $rows = collect();

        if ($ext === 'xml') {
            try {
                $xml = simplexml_load_file($request->file('file')->getRealPath());
                foreach ($xml->student ?? $xml->row ?? $xml->children() as $node) {
                    $rows->push((array) $node);
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Invalid XML file: ' . $e->getMessage());
            }
        } else {
            $rows = Excel::toCollection(new StudentsImport, $request->file('file'))->first() ?? collect();
        }

        if ($rows->isEmpty()) {
            return back()->with('error', 'The file appears to be empty or has no readable rows.');
        }

        // Preload all programs for flexible lookup (by code or name)
        $allPrograms = \App\Models\Program::all(['id', 'code', 'name']);
        $programs = $allPrograms->mapWithKeys(fn($p) => [strtoupper($p->code) => $p->id]);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $raw) {
            // Normalise keys: strip BOM, lowercase, spaces/hyphens → underscores
            $row = collect($raw)->mapWithKeys(function ($v, $k) {
                $key = strtolower(trim((string) $k));
                $key = ltrim($key, "\xEF\xBB\xBF");       // strip UTF-8 BOM from first column
                $key = str_replace([' ', '-'], '_', $key); // "Program Code" → "program_code"
                return [$key => trim((string) $v)];
            })->toArray();
            $rowNum = $index + 2;

            // ── Required fields ──────────────────────────────────────────────
            $firstName   = $row['first_name']    ?? $row['firstname']    ?? $row['given_name']  ?? '';
            $lastName    = $row['last_name']     ?? $row['lastname']     ?? $row['surname']     ?? $row['family_name'] ?? '';
            $email       = strtolower(trim($row['email'] ?? $row['email_address'] ?? ''));
            $programCode = strtoupper(trim(
                $row['program_code']  ?? $row['programme_code'] ??
                $row['programcode']   ?? $row['programme']      ??
                $row['program']       ?? ''
            ));

            // Specific missing-field messages
            $missing = [];
            if (!$firstName)   $missing[] = 'first_name';
            if (!$lastName)    $missing[] = 'last_name';
            if (!$email)       $missing[] = 'email';
            if (!$programCode) $missing[] = 'program_code';

            if ($missing) {
                $errors[] = "Row {$rowNum}: missing " . implode(', ', $missing) . ".";
                $skipped++;
                continue;
            }

            if (!\Illuminate\Support\Facades\Validator::make(['email' => $email], ['email' => 'email'])->passes()) {
                $errors[] = "Row {$rowNum}: invalid email address '{$email}'.";
                $skipped++;
                continue;
            }

            if (\App\Models\User::where('email', $email)->exists()) {
                $errors[] = "Row {$rowNum}: email '{$email}' is already registered.";
                $skipped++;
                continue;
            }

            $nrcCheck = $row['national_id'] ?? $row['nrc_number'] ?? $row['nrc'] ?? '';
            if ($nrcCheck && Student::where('national_id', $nrcCheck)->exists()) {
                $errors[] = "Row {$rowNum}: NRC number '{$nrcCheck}' is already assigned to another student.";
                $skipped++;
                continue;
            }

            // 1. Exact code match (case-insensitive)
            $programId = $programs[$programCode] ?? null;

            // 2. Fallback: partial name match (e.g. "ICT101" won't match, but "Computer Science" will)
            if (!$programId) {
                $matched = $allPrograms->first(
                    fn($p) => stripos($p->name, $programCode) !== false
                           || stripos($programCode, $p->code) !== false
                );
                $programId = $matched?->id;
            }

            if (!$programId) {
                $available = $allPrograms->map(fn($p) => "{$p->code} ({$p->name})")->implode(', ');
                $errors[] = "Row {$rowNum}: '{$programCode}' did not match any program code or name. Use one of: {$available}";
                $skipped++;
                continue;
            }

            // ── Optional fields (with sensible defaults) ─────────────────────
            $dob    = $this->normaliseDate($row['date_of_birth'] ?? $row['dob'] ?? $row['birth_date'] ?? '');
            $gender = strtolower(trim($row['gender'] ?? $row['sex'] ?? ''));

            if ($gender && !in_array($gender, ['male', 'female', 'other'])) {
                $errors[] = "Row {$rowNum}: gender '{$gender}' is invalid. Use: male, female, or other.";
                $skipped++;
                continue;
            }

            // ── Optional fields (matching create form names) ─────────────────
            $middleName   = $row['middle_name']  ?? $row['middlename'] ?? '';
            $phone        = $row['phone']        ?? '';
            $nationalId   = $row['national_id']  ?? $row['nrc_number'] ?? $row['nrc'] ?? '';
            $nationality  = ($row['nationality'] ?? '') ?: 'Zambian';
            $address      = $row['address']      ?? '';
            $sponsor      = $row['sponsor']      ?? '';
            $currentLevel = max(1, (int) ($row['year_of_study'] ?? $row['current_level'] ?? 1));

            $enrollmentDate = $this->normaliseDate($row['enrollment_date'] ?? $row['admission_date'] ?? '')
                            ?: now()->toDateString();

            $allowedTypes  = ['full-time', 'part-time', 'distance', 'online'];
            $admissionType = in_array($row['admission_type'] ?? $row['student_type'] ?? '', $allowedTypes)
                           ? ($row['admission_type'] ?? $row['student_type'])
                           : 'full-time';
            $password     = !empty($row['password']) ? $row['password'] : 'Student@123';

            $emergencyName  = $row['emergency_contact_name']  ?? $row['emergency_name']  ?? '';
            $emergencyPhone = $row['emergency_contact_phone'] ?? $row['emergency_phone'] ?? '';

            try {
                DB::transaction(function () use (
                    $firstName, $middleName, $lastName, $email, $phone,
                    $dob, $gender, $nationalId, $nationality, $address, $sponsor,
                    $programId, $currentLevel, $enrollmentDate, $admissionType, $password,
                    $emergencyName, $emergencyPhone
                ) {
                    $fullName = trim($firstName
                        . ($middleName ? ' ' . $middleName : '')
                        . ' ' . $lastName);

                    $user = \App\Models\User::create([
                        'name'      => $fullName,
                        'email'     => $email,
                        'password'  => Hash::make($password),
                        'phone'     => $phone ?: null,
                        'is_active' => true,
                    ]);
                    $user->assignRole('student');

                    $studentId = 'STU' . str_pad(Student::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT);

                    $student = Student::create([
                        'user_id'         => $user->id,
                        'student_id'      => $studentId,
                        'program_id'      => $programId,
                        'year_of_study'   => $currentLevel,
                        'admission_type'  => $admissionType,
                        'gender'          => $gender ?: null,
                        'date_of_birth'   => $dob ?: null,
                        'nationality'     => $nationality ?: 'Zambian',
                        'national_id'     => $nationalId ?: null,
                        'phone'           => $phone ?: null,
                        'address'         => $address ?: null,
                        'sponsor'         => $sponsor ?: null,
                        'enrollment_date' => $enrollmentDate ?: now()->toDateString(),
                        'status'          => 'active',
                    ]);

                    // Create emergency contact as guardian if provided
                    if ($emergencyName) {
                        $student->guardians()->create([
                            'name'                 => $emergencyName,
                            'relationship'         => 'Emergency Contact',
                            'phone'                => $emergencyPhone ?: 'N/A',
                            'is_emergency_contact' => true,
                        ]);
                    }
                });
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
                $skipped++;
            }
        }

        $msg = "{$imported} student(s) imported successfully.";
        if ($skipped) $msg .= " {$skipped} row(s) skipped.";
        // Surface the first error directly so it's visible even without scrolling
        if ($imported === 0 && !empty($errors)) {
            $msg .= ' — ' . $errors[0];
        }

        return back()
            ->with($imported > 0 ? 'success' : 'error', $msg)
            ->with('import_errors', $errors);
    }

    public function exportReport(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $query = Student::with(['user', 'program.department.faculty']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }
        if ($request->faculty_id) {
            $query->whereHas('program.department', fn($q) => $q->where('faculty_id', $request->faculty_id));
        }
        if ($request->program_id) $query->where('program_id', $request->program_id);
        if ($request->status)     $query->where('status', $request->status);
        if ($request->sponsor)    $query->where('sponsor', 'like', '%' . $request->sponsor . '%');

        $students = $query->orderBy('student_id')->get();

        $filters = array_filter([
            'Search'  => $request->search,
            'Faculty' => $request->faculty_id ? Faculty::find($request->faculty_id)?->name : null,
            'Program' => $request->program_id ? Program::find($request->program_id)?->name : null,
            'Status'  => $request->status ? ucfirst($request->status) : null,
            'Sponsor' => $request->sponsor,
        ]);

        $settingsPath = storage_path('app/settings.json');
        $settings = file_exists($settingsPath) ? (json_decode(file_get_contents($settingsPath), true) ?? []) : [];
        $uniName = $settings['university_name'] ?? config('app.name', 'University');
        $logoSrc = null;
        if (!empty($settings['logo_path'])) {
            $lf = storage_path('app/public/' . $settings['logo_path']);
            if (file_exists($lf)) {
                $logoSrc = 'data:' . mime_content_type($lf) . ';base64,' . base64_encode(file_get_contents($lf));
            }
        }

        $format = $request->get('format', 'pdf');

        if ($format === 'csv') {
            $totalDropouts = \App\Models\Student::where('status', 'dropped_out')->count();
            $callback = function () use ($students, $totalDropouts) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                // Summary block
                fputcsv($file, ['--- SUMMARY ---']);
                fputcsv($file, ['Total Students', $students->count()]);
                fputcsv($file, ['Active', $students->where('status', 'active')->count()]);
                fputcsv($file, ['Dropped Out (all time)', $totalDropouts]);
                fputcsv($file, ['Male', $students->where('gender', 'male')->count()]);
                fputcsv($file, ['Female', $students->where('gender', 'female')->count()]);
                fputcsv($file, ['Generated', now()->format('d M Y H:i')]);
                fputcsv($file, []);
                // Data
                fputcsv($file, ['Student ID', 'Name', 'Email', 'Phone', 'Gender', 'NRC Number', 'Program', 'Year', 'Sponsor', 'Status', 'Enrollment Date']);
                foreach ($students as $s) {
                    fputcsv($file, [
                        $s->student_id,
                        optional($s->user)->name,
                        optional($s->user)->email,
                        $s->phone,
                        ucfirst($s->gender ?? ''),
                        $s->national_id ?? '',
                        optional($s->program)->name,
                        $s->year_of_study,
                        $s->sponsor,
                        $s->status === 'dropped_out' ? 'Dropped Out' : ucfirst($s->status),
                        optional($s->enrollment_date)->format('Y-m-d') ?? '',
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="students_report_' . date('Y-m-d') . '.csv"',
            ]);
        }

        $pdf = Pdf::loadView('students.report-pdf', compact('students', 'filters', 'uniName', 'logoSrc'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('students_report_' . date('Y-m-d') . '.pdf');
    }

    public function search(Request $request)
    {
        $students = Student::with(['user', 'program'])
            ->where(function($q) use ($request) {
                $search = $request->q;
                $q->where('student_id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%'));
            })->limit(10)->get(['id', 'student_id', 'user_id', 'program_id', 'photo']);

        return response()->json($students->map(fn($s) => [
            'id'         => $s->id,
            'student_id' => $s->student_id,
            'full_name'  => $s->full_name,
            'program'    => $s->program?->name,
            'photo_url'  => $s->photo_url,
        ]));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Normalise various date formats to YYYY-MM-DD for MySQL.
     * Handles: YYYY-MM-DD, DD/MM/YYYY, MM/DD/YYYY, Excel serial integers.
     */
    private function normaliseDate(string $raw): string
    {
        $raw = trim($raw);
        if (!$raw) return '';

        // Excel date serial (e.g. 44927 = 2023-01-01)
        if (ctype_digit($raw) && (int) $raw > 1000) {
            try {
                $date = \Carbon\Carbon::createFromTimestamp(((int) $raw - 25569) * 86400);
                return $date->format('Y-m-d');
            } catch (\Throwable) {}
        }

        // DD/MM/YYYY or D/M/YYYY
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        // MM-DD-YYYY
        if (preg_match('#^(\d{2})-(\d{2})-(\d{4})$#', $raw, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);
        }

        // Already YYYY-MM-DD or similar ISO — return as-is
        return $raw;
    }

    // Format expected by payments/create.blade.php AJAX
    public function ajaxSearch(Request $request)
    {
        $students = Student::with(['user', 'program'])
            ->where(function($q) use ($request) {
                $search = $request->q ?? '';
                $q->where('student_id', 'like', '%' . $search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $search . '%'));
            })->limit(10)->get();

        return response()->json($students->map(fn($s) => [
            'id'         => $s->id,
            'student_id' => $s->student_id,
            'name'       => $s->full_name,
            'program'    => $s->program?->name ?? '',
        ]));
    }
}
