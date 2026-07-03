@extends('layouts.app')
@section('title', 'Add Employee')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Employee</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol></nav>
    </div>
</div>

<form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data" id="createEmployeeForm">
@csrf
{{-- Combined name for any legacy validation --}}
<input type="hidden" name="name" id="hiddenName">

{{-- Tabs --}}
<ul class="nav nav-tabs mb-0" id="createTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab-details">
            <i class="bi bi-person-fill me-1"></i> Employee Details
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab-docs" id="docsTabLink">
            <i class="bi bi-folder2-open me-1"></i> Documents
            <span class="badge bg-secondary ms-1" id="docCount">0</span>
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm" style="border-top-left-radius:0">
    <div class="card-body">
        <div class="tab-content">

            {{-- ── Details Tab ──────────────────────────────────── --}}
            <div class="tab-pane fade show active" id="tab-details">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                            value="{{ old('first_name') }}" required>
                        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                            value="{{ old('last_name') }}" required>
                        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror"
                            value="{{ old('designation') }}" required placeholder="e.g. Lecturer, Accountant">
                        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
                        <select name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
                            @foreach(['permanent' => 'Permanent', 'contract' => 'Contract', 'part-time' => 'Part-time'] as $val => $label)
                                <option value="{{ $val }}" {{ old('employment_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('employment_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Join Date <span class="text-danger">*</span></label>
                        <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror"
                            value="{{ old('join_date') }}" required>
                        @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Basic Salary (ZMW) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">K</span>
                            <input type="number" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror"
                                value="{{ old('basic_salary') }}" step="0.01" min="0" required>
                            @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">National ID (NRC Number)</label>
                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Initial Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Leave blank to use 'password'">
                    </div>
                </div>
            </div>

            {{-- ── Documents Tab ─────────────────────────────────── --}}
            <div class="tab-pane fade" id="tab-docs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Optionally attach documents now, or add them later from the employee's edit page.
                    </p>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addDocRow()">
                        <i class="bi bi-plus-circle me-1"></i> Add Document
                    </button>
                </div>

                <div id="docRows">
                    {{-- Rows added by JS --}}
                </div>

                <div id="docEmptyMsg" class="text-center text-muted py-4">
                    <i class="bi bi-folder2-open fs-2 d-block mb-2"></i>
                    No documents added yet. Click "Add Document" to attach files.
                </div>
            </div>

        </div>{{-- tab-content --}}

        <hr class="my-3">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i> Save Employee
            </button>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
// Keep hidden name in sync with first/last name inputs
document.addEventListener('DOMContentLoaded', function () {
    function syncName() {
        const fn = document.querySelector('[name="first_name"]')?.value || '';
        const mn = document.querySelector('[name="middle_name"]')?.value || '';
        const ln = document.querySelector('[name="last_name"]')?.value || '';
        document.getElementById('hiddenName').value = [fn, mn, ln].filter(Boolean).join(' ');
    }
    ['first_name','middle_name','last_name'].forEach(n => {
        document.querySelector('[name="' + n + '"]')?.addEventListener('input', syncName);
    });
});

let docIndex = 0;
const docTypes = {
    nrc:           { label: 'NRC',                        icon: 'bi-person-vcard',  badge: 'bg-info' },
    cv:            { label: 'CV / Resume',                icon: 'bi-file-person',   badge: 'bg-primary' },
    qualification: { label: 'Professional Qualification', icon: 'bi-award',         badge: 'bg-success' },
    accreditation: { label: 'Accreditation / Licence',   icon: 'bi-patch-check',   badge: 'bg-warning text-dark' },
};

function addDocRow() {
    const i = docIndex++;
    const row = document.createElement('div');
    row.className = 'card border mb-2 p-3';
    row.id = 'docRow_' + i;
    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Type <span class="text-danger">*</span></label>
                <select name="documents[${i}][type]" class="form-select form-select-sm" required>
                    ${Object.entries(docTypes).map(([v,d]) => `<option value="${v}">${d.label}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label form-label-sm">Title <span class="text-danger">*</span></label>
                <input type="text" name="documents[${i}][title]" class="form-control form-control-sm"
                    placeholder="e.g. NRC Front Page" required>
            </div>
            <div class="col-md-4">
                <label class="form-label form-label-sm">File <span class="text-danger">*</span></label>
                <input type="file" name="documents[${i}][file]" class="form-control form-control-sm"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                <div class="form-text">PDF, DOC, DOCX, JPG, PNG — max 10 MB</div>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocRow(${i})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    document.getElementById('docRows').appendChild(row);
    document.getElementById('docEmptyMsg').style.display = 'none';
    updateDocCount();
}

function removeDocRow(i) {
    document.getElementById('docRow_' + i)?.remove();
    const hasRows = document.querySelectorAll('#docRows .card').length > 0;
    document.getElementById('docEmptyMsg').style.display = hasRows ? 'none' : '';
    updateDocCount();
}

function updateDocCount() {
    const n = document.querySelectorAll('#docRows .card').length;
    document.getElementById('docCount').textContent = n;
    document.getElementById('docCount').className = 'badge ms-1 ' + (n > 0 ? 'bg-primary' : 'bg-secondary');
}
</script>
@endpush
@endsection
