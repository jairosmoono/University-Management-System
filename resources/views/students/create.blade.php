@extends('layouts.app')
@section('title', 'Add Student')
@section('page-title', 'Add Student')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-plus me-2" style="color:var(--secondary)"></i>Add New Student</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
        <li class="breadcrumb-item active">Add Student</li>
    </ol></nav>
</div>

<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">
        <!-- Personal Info -->
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+260 XXX XXXXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select...</option>
                                <option value="male"   {{ old('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other"  {{ old('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">National ID</label>
                            <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}" placeholder="000000/00/0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="{{ old('nationality', 'Zambian') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sponsor / Funding Source</label>
                            <input type="text" name="sponsor" class="form-control" value="{{ old('sponsor') }}" placeholder="e.g. Self, Government Bursary, NGO…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
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
                                <option value="{{ $f->id }}" {{ old('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <select name="department_id" id="department_id" class="form-select" onchange="loadPrograms(this.value)">
                                <option value="">All Departments...</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Program <span class="text-danger">*</span></label>
                            <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                <option value="">Select Program...</option>
                                @foreach($programs as $p)
                                <option value="{{ $p->id }}" {{ old('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->code }})</option>
                                @endforeach
                            </select>
                            @error('program_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Admission Type</label>
                            <select name="admission_type" class="form-select">
                                <option value="full-time" {{ old('admission_type', 'full-time') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('admission_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="distance"  {{ old('admission_type') == 'distance'  ? 'selected' : '' }}>Distance</option>
                                <option value="online"    {{ old('admission_type') == 'online'    ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Year of Study</label>
                            <select name="year_of_study" class="form-select">
                                @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ old('year_of_study', 1) == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Enrollment Date <span class="text-danger">*</span></label>
                            <input type="date" name="enrollment_date" class="form-control @error('enrollment_date') is-invalid @enderror" value="{{ old('enrollment_date', date('Y-m-d')) }}" required>
                            @error('enrollment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Expected Graduation</label>
                            <input type="date" name="expected_graduation" class="form-control" value="{{ old('expected_graduation') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo & Actions -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-image me-2"></i>Passport Photo</h6>
                </div>
                <div class="card-body text-center">
                    <div id="photo-preview" class="mx-auto mb-3 rounded" style="width:140px;height:175px;background:#f0f4f8;border:2px dashed #dee2e6;display:flex;align-items:center;justify-content:center;overflow:hidden">
                        <span class="text-muted" id="preview-placeholder"><i class="bi bi-person fs-1 d-block"></i><small>No photo</small></span>
                        <img id="preview-img" src="" style="display:none;width:100%;height:100%;object-fit:cover" alt="">
                    </div>
                    <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewPhoto(this)">
                    <label for="photo" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-upload me-1"></i>Upload Photo</label>
                    <div class="text-muted mt-1" style="font-size:0.75rem">JPG, PNG. Max 2MB</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-key me-2"></i>Account Password</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank for default">
                        <div class="form-text">Default: Student@123</div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-telephone-fill me-2"></i>Emergency Contact</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary text-white"><i class="bi bi-save me-1"></i>Register Student</button>
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-img').style.display = 'block';
            document.getElementById('preview-placeholder').style.display = 'none';
        };
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
