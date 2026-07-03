@extends('layouts.app')
@section('title', 'Edit Student')
@section('page-title', 'Edit Student')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square me-2" style="color:var(--secondary)"></i>Edit Student</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.show', $student) }}">{{ $student->student_id }}</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol></nav>
</div>

<form method="POST" action="{{ route('students.update', $student) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-3">
        <!-- Personal Info -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $nameParts  = explode(' ', $student->user->name ?? '', 3);
                            $firstName  = old('first_name',  $nameParts[0] ?? '');
                            $middleName = old('middle_name', isset($nameParts[2]) ? $nameParts[1] : '');
                            $lastName   = old('last_name',   isset($nameParts[2]) ? $nameParts[2] : ($nameParts[1] ?? ''));
                        @endphp
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ $firstName }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ $middleName }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ $lastName }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $student->user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $student->phone) }}" placeholder="+260 XXX XXXXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select...</option>
                                @foreach(['male','female','other'] as $g)
                                <option value="{{ $g }}" {{ old('gender', $student->gender) == $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">National ID</label>
                            <input type="text" name="national_id" class="form-control"
                                value="{{ old('national_id', $student->national_id) }}" placeholder="000000/00/0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nationality</label>
                            <input type="text" name="nationality" class="form-control"
                                value="{{ old('nationality', $student->nationality) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sponsor / Funding Source</label>
                            <input type="text" name="sponsor" class="form-control"
                                value="{{ old('sponsor', $student->sponsor) }}"
                                placeholder="e.g. Self, Government Bursary, NGO…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Info -->
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Faculty</label>
                            <select name="faculty_id" id="faculty_id" class="form-select" onchange="loadDepartments(this.value)">
                                <option value="">All Faculties...</option>
                                @foreach($faculties as $f)
                                <option value="{{ $f->id }}"
                                    {{ old('faculty_id', $student->program?->department?->faculty_id) == $f->id ? 'selected' : '' }}>
                                    {{ $f->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="department_id" class="form-select" onchange="loadPrograms(this.value)">
                                <option value="">All Departments...</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}"
                                    {{ old('department_id', $student->program?->department_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                            <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                <option value="">Select Program...</option>
                                @foreach($programs as $p)
                                <option value="{{ $p->id }}" {{ old('program_id', $student->program_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Type</label>
                            <select name="admission_type" class="form-select">
                                <option value="full-time" {{ old('admission_type', $student->admission_type) == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('admission_type', $student->admission_type) == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="distance"  {{ old('admission_type', $student->admission_type) == 'distance'  ? 'selected' : '' }}>Distance</option>
                                <option value="online"    {{ old('admission_type', $student->admission_type) == 'online'    ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year of Study</label>
                            <select name="year_of_study" class="form-select">
                                @for($y = 1; $y <= 6; $y++)
                                <option value="{{ $y }}" {{ old('year_of_study', $student->year_of_study) == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Enrollment Date</label>
                            <input type="date" name="enrollment_date" class="form-control"
                                value="{{ old('enrollment_date', $student->enrollment_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expected Graduation</label>
                            <input type="date" name="expected_graduation" class="form-control"
                                value="{{ old('expected_graduation', $student->expected_graduation?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                @php $statusLabels = ['active'=>'Active','inactive'=>'Inactive','suspended'=>'Suspended','deferred'=>'Deferred','graduated'=>'Graduated','dropped_out'=>'Dropped Out']; @endphp
                                @foreach($statusLabels as $val => $lbl)
                                <option value="{{ $val }}" {{ old('status', $student->status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-image me-2"></i>Profile Photo</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mx-auto mb-3 rounded overflow-hidden"
                         style="width:140px;height:175px;background:#f0f4f8;border:2px dashed #dee2e6">
                        <img id="photoPreview" src="{{ $student->photo_url }}"
                             style="width:100%;height:100%;object-fit:cover" alt="Photo">
                    </div>
                    <input type="file" name="photo" id="photo" class="d-none" accept="image/*"
                        onchange="previewPhoto(this)">
                    <label for="photo" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-camera me-1"></i>Change Photo
                    </label>
                    <div class="text-muted mt-1" style="font-size:0.75rem">JPG, PNG. Max 2MB</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-key me-2"></i>Reset Password</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Leave blank to keep current">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>

            @php $emergencyContact = $student->guardians->where('is_emergency_contact', true)->first(); @endphp
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h6>
                </div>
                <div class="card-body">
                    @if($emergencyContact)
                    <div class="mb-2 p-2 bg-light rounded" style="font-size:0.85rem">
                        <div class="fw-semibold">{{ $emergencyContact->name }}</div>
                        <div class="text-muted">{{ $emergencyContact->phone }}</div>
                        <div class="text-muted">{{ ucfirst($emergencyContact->relationship) }}</div>
                    </div>
                    <a href="{{ route('students.show', $student) }}#guardian"
                       class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-people me-1"></i>Manage Guardians
                    </a>
                    @else
                    <p class="text-muted small mb-2">No emergency contact on record.</p>
                    <a href="{{ route('students.show', $student) }}#guardian"
                       class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-person-plus me-1"></i>Add Guardian
                    </a>
                    @endif
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary text-white">
                    <i class="bi bi-save me-1"></i>Save Changes
                </button>
                <a href="{{ route('students.show', $student) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('photoPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
function loadDepartments(facultyId) {
    if (!facultyId) return;
    fetch(`/ajax/departments/by-faculty/${facultyId}`)
        .then(r => r.json()).then(data => {
            const sel = document.getElementById('department_id');
            sel.innerHTML = '<option value="">All Departments...</option>';
            data.forEach(d => sel.innerHTML += `<option value="${d.id}">${d.name}</option>`);
            document.getElementById('program_id').innerHTML = '<option value="">Select Program...</option>';
        });
}
function loadPrograms(deptId) {
    if (!deptId) return;
    fetch(`/ajax/programs/by-department/${deptId}`)
        .then(r => r.json()).then(data => {
            const sel = document.getElementById('program_id');
            sel.innerHTML = '<option value="">Select Program...</option>';
            data.forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name} (${p.code})</option>`);
        });
}
</script>
@endpush
