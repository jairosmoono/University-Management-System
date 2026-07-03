@extends('layouts.app')
@section('title', 'Edit Employee')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Employee</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
    <a href="{{ route('hr.employees.show', $employee) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye me-1"></i> View Profile
    </a>
</div>

{{-- Tabs --}}
@php $activeTab = session('_activeTab', 'details'); @endphp
<ul class="nav nav-tabs mb-0" id="editTabs">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'details' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#tab-details">
            <i class="bi bi-person-fill me-1"></i> Employee Details
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'documents' ? 'active' : '' }}"
            data-bs-toggle="tab" href="#tab-docs">
            <i class="bi bi-folder2-open me-1"></i> Documents
            <span class="badge {{ $documents->count() > 0 ? 'bg-primary' : 'bg-secondary' }} ms-1">
                {{ $documents->count() }}
            </span>
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm" style="border-top-left-radius:0">
    <div class="card-body">
        <div class="tab-content">

            {{-- ── Details Tab ──────────────────────────────────── --}}
            <div class="tab-pane fade {{ $activeTab === 'details' ? 'show active' : '' }}"
                id="tab-details">
                <form action="{{ route('hr.employees.update', $employee) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', optional($employee->user)->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Employee ID</label>
                            <input type="text" class="form-control" value="{{ $employee->employee_id }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', optional($employee->user)->email) }}"
                                placeholder="employee@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', optional($employee->user)->phone) }}"
                                placeholder="+260 9XX XXX XXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control"
                                value="{{ old('designation', $employee->designation) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-select">
                                @foreach(['permanent' => 'Permanent', 'contract' => 'Contract', 'part-time' => 'Part-time'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('employment_type', $employee->employment_type) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['active' => 'Active', 'inactive' => 'Inactive', 'terminated' => 'Terminated', 'on_leave' => 'On Leave'] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('status', $employee->status) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Basic Salary (ZMW)</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="basic_salary" class="form-control"
                                    value="{{ old('basic_salary', $employee->basic_salary) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">National ID (NRC Number)</label>
                            <input type="text" name="national_id" class="form-control"
                                value="{{ old('national_id', $employee->national_id) }}">
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Employee
                        </button>
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>

            {{-- ── Documents Tab ─────────────────────────────────── --}}
            <div class="tab-pane fade {{ $activeTab === 'documents' ? 'show active' : '' }}" id="tab-docs">

                {{-- Upload Form --}}
                <div class="card bg-light border mb-4">
                    <div class="card-header bg-transparent fw-semibold py-2">
                        <i class="bi bi-cloud-upload me-1"></i> Upload New Document
                    </div>
                    <div class="card-body">
                        <form action="{{ route('hr.employees.documents.upload', $employee) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                    <select name="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                                        <option value="">— Select —</option>
                                        <option value="nrc"           {{ old('document_type') === 'nrc'           ? 'selected' : '' }}>NRC</option>
                                        <option value="cv"            {{ old('document_type') === 'cv'            ? 'selected' : '' }}>CV / Resume</option>
                                        <option value="qualification" {{ old('document_type') === 'qualification' ? 'selected' : '' }}>Professional Qualification</option>
                                        <option value="accreditation" {{ old('document_type') === 'accreditation' ? 'selected' : '' }}>Accreditation / Licence</option>
                                    </select>
                                    @error('document_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Title / Description <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" placeholder="e.g. Bachelor of Education Certificate" required>
                                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">File <span class="text-danger">*</span></label>
                                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                    <div class="form-text">PDF, DOC, DOCX, JPG, PNG — max 10 MB</div>
                                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Existing Documents --}}
                @if($documents->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                        No documents uploaded yet.
                    </div>
                @else
                    @php
                        $typeOrder = ['nrc', 'cv', 'qualification', 'accreditation'];
                        $grouped   = $documents->groupBy('document_type');
                    @endphp
                    @foreach($typeOrder as $type)
                        @if($grouped->has($type))
                        @php
                            $typeLabels = ['nrc' => 'NRC', 'cv' => 'CV / Resume', 'qualification' => 'Professional Qualifications', 'accreditation' => 'Accreditations / Licences'];
                            $typeIcons  = ['nrc' => 'bi-person-vcard', 'cv' => 'bi-file-person', 'qualification' => 'bi-award', 'accreditation' => 'bi-patch-check'];
                            $typeBadges = ['nrc' => 'bg-info', 'cv' => 'bg-primary', 'qualification' => 'bg-success', 'accreditation' => 'bg-warning text-dark'];
                        @endphp
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">
                                <i class="bi {{ $typeIcons[$type] }} me-1"></i>
                                {{ $typeLabels[$type] }}
                                <span class="badge {{ $typeBadges[$type] }} ms-1">{{ $grouped[$type]->count() }}</span>
                            </h6>
                            <div class="list-group">
                                @foreach($grouped[$type] as $doc)
                                <div class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-2">
                                    @php
                                        $isImage = in_array(strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION)), ['jpg','jpeg','png']);
                                        $isPdf   = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION)) === 'pdf';
                                    @endphp
                                    <i class="bi {{ $isPdf ? 'bi-file-earmark-pdf text-danger' : ($isImage ? 'bi-file-earmark-image text-info' : 'bi-file-earmark-word text-primary') }} fs-4 flex-shrink-0"></i>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold text-truncate">{{ $doc->title }}</div>
                                        <small class="text-muted">
                                            {{ $doc->file_name }}
                                            &bull; {{ $doc->file_size_human }}
                                            &bull; {{ $doc->created_at->format('d M Y') }}
                                            @if($doc->uploadedBy)
                                                &bull; by {{ $doc->uploadedBy->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="d-flex gap-1 flex-shrink-0">
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary" title="View / Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('hr.employees.documents.destroy', $doc) }}" method="POST"
                                            onsubmit="return confirm('Delete document \'{{ addslashes($doc->title) }}\'?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>

        </div>{{-- tab-content --}}
    </div>
</div>

@push('scripts')
<script>
// Keep active tab after page reload (e.g. after upload)
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;
    if (hash === '#tab-docs') {
        const tab = document.querySelector('[href="#tab-docs"]');
        if (tab) new bootstrap.Tab(tab).show();
    }

    document.querySelectorAll('#editTabs .nav-link').forEach(function(link) {
        link.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, '', e.target.getAttribute('href'));
        });
    });
});
</script>
@endpush
@endsection
