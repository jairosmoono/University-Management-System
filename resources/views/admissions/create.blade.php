@extends('layouts.app')
@section('title', 'New Application')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">New Admission Application</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admissions.index') }}">Admissions</a></li>
            <li class="breadcrumb-item active">New Application</li>
        </ol></nav>
    </div>
</div>

<form method="POST" action="{{ route('admissions.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Personal Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-person me-2 text-primary"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" value="{{ old('nationality', 'Zambian') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="+260971...">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Home Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2 text-primary"></i>Academic Background</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Previous School / Institution</label>
                            <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Qualification Type</label>
                            <select name="qualification_type" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="Grade 12 Certificate" {{ old('qualification_type') == 'Grade 12 Certificate' ? 'selected' : '' }}>Grade 12 Certificate</option>
                                <option value="Diploma" {{ old('qualification_type') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                <option value="Bachelor's Degree" {{ old('qualification_type') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                <option value="Master's Degree" {{ old('qualification_type') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                <option value="Other" {{ old('qualification_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year Completed</label>
                            <input type="number" name="year_completed" class="form-control" value="{{ old('year_completed') }}" min="1990" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grade / GPA</label>
                            <input type="text" name="grade" class="form-control" value="{{ old('grade') }}" placeholder="e.g., Merit, 3.8 GPA">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Supporting Documents -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-paperclip me-2 text-primary"></i>Supporting Documents</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Academic Certificates</label>
                            <input type="file" name="documents[certificates]" class="form-control" accept=".pdf,.jpg,.png">
                            <small class="text-muted">PDF, JPG, PNG. Max 5MB</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">National ID / Passport</label>
                            <input type="file" name="documents[national_id]" class="form-control" accept=".pdf,.jpg,.png">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Passport Photo</label>
                            <input type="file" name="documents[photo]" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Other Documents</label>
                            <input type="file" name="documents[other]" class="form-control" accept=".pdf,.jpg,.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3 sticky-top" style="top: 80px">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Application Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Applying for Semester/Term *</label>
                        <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                            <option value="">-- Select Semester/Term --</option>
                            @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty *</label>
                        <select id="faculty_select" class="form-select" onchange="loadPrograms(this.value)">
                            <option value="">-- Select Faculty --</option>
                            @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Program *</label>
                        <select name="program_id" id="program_select" class="form-select @error('program_id') is-invalid @enderror" required>
                            <option value="">-- Select Faculty First --</option>
                        </select>
                        @error('program_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Submit Application
                        </button>
                        <a href="{{ route('admissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex gap-2 mb-2">
                        <i class="bi bi-info-circle text-primary mt-1"></i>
                        <div>
                            <strong class="d-block" style="font-size:0.85rem">Application Note</strong>
                            <small class="text-muted">Applications are reviewed within 5-7 business days. Status updates will be sent by email.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function loadPrograms(facultyId) {
    const select = document.getElementById('program_select');
    select.innerHTML = '<option value="">Loading...</option>';
    if (!facultyId) {
        select.innerHTML = '<option value="">-- Select Faculty First --</option>';
        return;
    }
    fetch(`/ajax/programs?faculty_id=${facultyId}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Select Program --</option>';
            data.forEach(p => {
                select.innerHTML += `<option value="${p.id}">${p.name} (${p.code})</option>`;
            });
        })
        .catch(() => { select.innerHTML = '<option value="">Error loading</option>'; });
}
</script>
@endpush
