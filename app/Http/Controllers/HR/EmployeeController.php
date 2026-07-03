<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department']);
        if ($request->department_id)   $query->where('department_id', $request->department_id);
        if ($request->employment_type) $query->where('employment_type', $request->employment_type);
        if ($request->status)          $query->where('status', $request->status);
        if ($request->search) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('employee_id', 'like', "%$s%")
                ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%$s%"))
            );
        }
        $employees   = $query->orderBy('employee_id')->paginate(20);
        $departments = Department::all();
        $stats = [
            'total'     => Employee::count(),
            'permanent' => Employee::where('employment_type', 'permanent')->count(),
            'contract'  => Employee::where('employment_type', 'contract')->count(),
            'on_leave'  => LeaveRequest::where('status', 'approved')
                               ->where('start_date', '<=', today())
                               ->where('end_date', '>=', today())
                               ->count(),
        ];
        return view('hr.employees.index', compact('employees', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('hr.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'designation'      => 'required|string|max:100',
            'employment_type'  => 'required|in:permanent,contract,part-time',
            'join_date'        => 'required|date',
            'basic_salary'     => 'required|numeric|min:0',
            'documents.*.file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'documents.*.type' => 'nullable|in:nrc,cv,qualification,accreditation',
        ]);

        $fullName = trim($request->first_name . ' ' . ($request->middle_name ? $request->middle_name . ' ' : '') . $request->last_name);

        $user = User::create([
            'name'     => $fullName,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password ?? 'password'),
            'role'     => 'staff',
        ]);

        $count = Employee::withTrashed()->count() + 1;
        $employee = Employee::create([
            'user_id'         => $user->id,
            'employee_id'     => 'EMP/' . date('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'department_id'   => $request->department_id,
            'designation'     => $request->designation,
            'employment_type' => $request->employment_type,
            'join_date'       => $request->join_date,
            'basic_salary'    => $request->basic_salary,
            'national_id'     => $request->national_id,
            'status'          => 'active',
        ]);

        // Upload any documents submitted with the creation form
        foreach ($request->input('documents', []) as $idx => $docData) {
            $fileKey = "documents.{$idx}.file";
            if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                $file = $request->file($fileKey);
                $path = $file->store("employee_documents/{$employee->id}", 'public');
                EmployeeDocument::create([
                    'employee_id'   => $employee->id,
                    'document_type' => $docData['type'] ?? 'cv',
                    'title'         => $docData['title'] ?? $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getMimeType(),
                    'uploaded_by'   => auth()->id(),
                ]);
            }
        }

        return redirect()->route('hr.employees.edit', $employee)
            ->with('success', 'Employee added. You can upload additional documents below.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'department', 'leaveRequests', 'payrolls', 'documents']);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $documents   = $employee->documents()->latest()->get();
        return view('hr.employees.edit', compact('employee', 'departments', 'documents'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'email' => 'nullable|email|unique:users,email,' . ($employee->user_id ?? 0),
        ]);

        $employee->update($request->only([
            'department_id', 'designation', 'employment_type',
            'join_date', 'contract_end_date', 'basic_salary',
            'bank_name', 'bank_account', 'national_id', 'status',
        ]));

        $userUpdates = [];
        if ($request->filled('name'))  $userUpdates['name']  = $request->name;
        if ($request->filled('email')) $userUpdates['email'] = $request->email;
        if ($request->has('phone'))    $userUpdates['phone'] = $request->phone;
        if ($userUpdates) $employee->user?->update($userUpdates);

        return redirect()->route('hr.employees.edit', $employee)
            ->with('success', 'Employee details updated.')
            ->with('_activeTab', 'details');
    }

    public function destroy(Employee $employee)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403);
        }

        $user = $employee->user;

        // Delete uploaded documents from storage
        foreach ($employee->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        // Hard-delete child rows that have no CASCADE on their FK
        DB::table('leave_requests')->where('employee_id', $employee->id)->delete();
        DB::table('payroll')->where('employee_id', $employee->id)->delete();

        $employee->forceDelete();

        if ($user) {
            // RESTRICT FK: must delete before the user row
            DB::table('messages')->where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();
            DB::table('announcements')->where('user_id', $user->id)->delete();
            $user->forceDelete();
        }

        return redirect()->route('hr.employees.index')->with('success', 'Employee and their account have been permanently deleted.');
    }

    public function uploadDocument(Request $request, Employee $employee)
    {
        $request->validate(
            [
                'document_type' => 'required|in:nrc,cv,qualification,accreditation',
                'title'         => 'required|string|max:200',
                'file'          => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
            [],
            [
                'document_type' => 'document type',
                'title'         => 'title',
                'file'          => 'file',
            ]
        );
        // Keep user on Documents tab even if redirect happens after this point
        session()->flash('_activeTab', 'documents');

        $file = $request->file('file');
        $path = $file->store("employee_documents/{$employee->id}", 'public');

        EmployeeDocument::create([
            'employee_id'   => $employee->id,
            'document_type' => $request->document_type,
            'title'         => $request->title,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'uploaded_by'   => auth()->id(),
        ]);

        return redirect()->route('hr.employees.edit', $employee)
            ->with('success', 'Document "' . $request->title . '" uploaded successfully.')
            ->with('_activeTab', 'documents');
    }

    public function destroyDocument(EmployeeDocument $document)
    {
        $employee = $document->employee;
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('hr.employees.edit', $employee)
            ->with('success', 'Document deleted.')
            ->with('_activeTab', 'documents');
    }

    public function bulkUploadForm()
    {
        $departments = Department::orderBy('name')->get();
        return view('hr.employees.bulk-upload', compact('departments'));
    }

    public function downloadTemplate()
    {
        $headers = ['first_name', 'middle_name', 'last_name', 'email', 'phone', 'national_id', 'department_id', 'designation', 'employment_type', 'join_date', 'basic_salary', 'bank_name', 'bank_account', 'sort_code', 'bank_branch'];
        $example = ['John', 'M', 'Banda', 'john.banda@email.com', '0971234567', '123456/78/1', '1', 'Lecturer', 'permanent', '2026-01-15', '8500.00', 'Zanaco', '0012345678', '01-02-03', 'Cairo Road'];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="employee_upload_template.csv"',
        ]);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        set_time_limit(300);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');
        $headerRow = fgetcsv($file);

        if (!$headerRow) {
            fclose($file);
            return back()->with('error', 'The uploaded file is empty.');
        }

        $headerRow = array_map(fn($h) => strtolower(trim(preg_replace('/^\x{FEFF}/u', '', $h))), $headerRow);

        $required = ['first_name', 'last_name', 'email', 'designation', 'employment_type', 'join_date', 'basic_salary'];
        $missing = array_diff($required, $headerRow);
        if ($missing) {
            fclose($file);
            return back()->with('error', 'Missing required columns: ' . implode(', ', $missing));
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        $validTypes = ['permanent', 'contract', 'part-time'];
        $deptIds    = Department::pluck('id')->toArray();

        while (($row = fgetcsv($file)) !== false) {
            $rowNum++;

            if (count($row) !== count($headerRow)) {
                $errors[] = "Row {$rowNum}: Column count mismatch (expected " . count($headerRow) . ", got " . count($row) . ")";
                $skipped++;
                continue;
            }

            $data = array_combine($headerRow, $row);
            $data = array_map('trim', $data);

            $firstName = $data['first_name'] ?? '';
            $lastName  = $data['last_name'] ?? '';
            $email     = $data['email'] ?? '';

            if (!$firstName || !$lastName || !$email) {
                $errors[] = "Row {$rowNum}: first_name, last_name, and email are required.";
                $skipped++;
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Row {$rowNum}: Invalid email '{$email}'.";
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Row {$rowNum}: Email '{$email}' already exists.";
                $skipped++;
                continue;
            }

            $empType = strtolower($data['employment_type'] ?? 'permanent');
            if (!in_array($empType, $validTypes)) {
                $errors[] = "Row {$rowNum}: Invalid employment_type '{$empType}'. Must be: " . implode(', ', $validTypes);
                $skipped++;
                continue;
            }

            $deptId = $data['department_id'] ?? null;
            if ($deptId && !in_array((int)$deptId, $deptIds)) {
                $errors[] = "Row {$rowNum}: department_id '{$deptId}' not found.";
                $skipped++;
                continue;
            }

            $salary = $data['basic_salary'] ?? 0;
            if (!is_numeric($salary) || $salary < 0) {
                $errors[] = "Row {$rowNum}: Invalid basic_salary '{$salary}'.";
                $skipped++;
                continue;
            }

            $joinDate = $data['join_date'] ?? date('Y-m-d');
            if (!strtotime($joinDate)) {
                $errors[] = "Row {$rowNum}: Invalid join_date '{$joinDate}'.";
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($data, $firstName, $lastName, $email, $empType, $deptId, $salary, $joinDate) {
                    $middleName = $data['middle_name'] ?? '';
                    $fullName = trim($firstName . ' ' . ($middleName ? $middleName . ' ' : '') . $lastName);

                    $user = User::create([
                        'name'     => $fullName,
                        'email'    => $email,
                        'phone'    => $data['phone'] ?? null,
                        'password' => Hash::make('password'),
                        'role'     => 'staff',
                    ]);

                    $count = Employee::withTrashed()->count() + 1;
                    Employee::create([
                        'user_id'         => $user->id,
                        'employee_id'     => 'EMP/' . date('Y') . '/' . str_pad($count, 4, '0', STR_PAD_LEFT),
                        'department_id'   => $deptId ?: null,
                        'designation'     => $data['designation'] ?? 'Staff',
                        'employment_type' => $empType,
                        'join_date'       => $joinDate,
                        'basic_salary'    => $salary,
                        'national_id'     => $data['national_id'] ?? null,
                        'bank_name'       => $data['bank_name'] ?? null,
                        'bank_account'    => $data['bank_account'] ?? null,
                        'sort_code'       => $data['sort_code'] ?? null,
                        'bank_branch'     => $data['bank_branch'] ?? null,
                        'status'          => 'active',
                    ]);
                });
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNum}: " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($file);

        return back()
            ->with('imported', $imported)
            ->with('skipped', $skipped)
            ->with('import_errors', array_slice($errors, 0, 20));
    }
}
