@extends('layouts.app')
@section('title', 'Bulk Employee Upload')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Bulk Employee Upload</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active">Bulk Upload</li>
        </ol></nav>
    </div>
    <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Employees
    </a>
</div>

{{-- Results --}}
@if(session('imported') !== null)
<div class="alert alert-{{ session('skipped') > 0 ? 'warning' : 'success' }} alert-dismissible fade show">
    <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-{{ session('skipped') > 0 ? 'exclamation-triangle' : 'check-circle' }} fs-5"></i>
        <strong>Import Complete</strong>
    </div>
    <div>
        <span class="badge bg-success me-1">{{ session('imported') }} imported</span>
        @if(session('skipped') > 0)
        <span class="badge bg-danger">{{ session('skipped') }} skipped</span>
        @endif
    </div>
    @if(session('import_errors'))
    <hr class="my-2">
    <details>
        <summary class="fw-semibold" style="cursor:pointer;font-size:.88rem">
            <i class="bi bi-list-ul me-1"></i>View errors ({{ count(session('import_errors')) }})
        </summary>
        <ul class="mt-2 mb-0" style="font-size:.83rem;max-height:300px;overflow-y:auto">
            @foreach(session('import_errors') as $err)
            <li class="text-danger">{{ $err }}</li>
            @endforeach
        </ul>
    </details>
    @endif
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">

    {{-- Upload Card --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload CSV File</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('hr.employees.bulk-upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf

                    <div class="mb-4">
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center position-relative" id="dropZone" style="border-color:#dee2e6;cursor:pointer;transition:all .2s">
                            <input type="file" name="file" id="csvFile" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor:pointer" accept=".csv" required>
                            <div id="dropContent">
                                <i class="bi bi-file-earmark-spreadsheet text-primary" style="font-size:2.5rem"></i>
                                <p class="fw-semibold mt-2 mb-1">Drag & drop your CSV file here</p>
                                <p class="text-muted small mb-0">or click to browse — max 5MB, .csv format</p>
                            </div>
                            <div id="fileInfo" class="d-none">
                                <i class="bi bi-file-earmark-check text-success" style="font-size:2rem"></i>
                                <p class="fw-semibold mt-2 mb-0" id="fileName"></p>
                                <p class="text-muted small mb-0" id="fileSize"></p>
                            </div>
                        </div>
                        @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-light border small mb-3">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        All imported employees will be created with the default password: <code>password</code>.
                        Employees should change their password on first login.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4" id="uploadBtn" disabled>
                            <i class="bi bi-upload me-1"></i>Import Employees
                        </button>
                        <a href="{{ route('hr.employees.bulk-upload.template') }}" class="btn btn-outline-success">
                            <i class="bi bi-download me-1"></i>Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Instructions Card --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-journal-text me-2 text-primary"></i>Instructions</h6>
            </div>
            <div class="card-body" style="font-size:.88rem">
                <ol class="mb-0">
                    <li class="mb-2">Click <strong>Download Template</strong> to get the CSV file with correct column headers.</li>
                    <li class="mb-2">Fill in employee data — one row per employee. Do not change the header row.</li>
                    <li class="mb-2">Save the file as <strong>.csv</strong> (comma-separated values) with UTF-8 encoding.</li>
                    <li class="mb-2">Upload the file and click <strong>Import Employees</strong>.</li>
                    <li>Review the results — any skipped rows will show with an error message.</li>
                </ol>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2 text-success"></i>Column Reference</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" style="font-size:.82rem">
                    <thead class="table-light">
                        <tr><th>Column</th><th>Required</th><th>Notes</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="fw-semibold">first_name</td><td><span class="badge bg-danger">Yes</span></td><td>-</td></tr>
                        <tr><td class="fw-semibold">middle_name</td><td><span class="badge bg-secondary">No</span></td><td>-</td></tr>
                        <tr><td class="fw-semibold">last_name</td><td><span class="badge bg-danger">Yes</span></td><td>-</td></tr>
                        <tr><td class="fw-semibold">email</td><td><span class="badge bg-danger">Yes</span></td><td>Must be unique</td></tr>
                        <tr><td class="fw-semibold">phone</td><td><span class="badge bg-secondary">No</span></td><td>-</td></tr>
                        <tr><td class="fw-semibold">national_id</td><td><span class="badge bg-secondary">No</span></td><td>NRC / ID number</td></tr>
                        <tr><td class="fw-semibold">department_id</td><td><span class="badge bg-secondary">No</span></td><td>Numeric ID (see below)</td></tr>
                        <tr><td class="fw-semibold">designation</td><td><span class="badge bg-danger">Yes</span></td><td>Job title</td></tr>
                        <tr><td class="fw-semibold">employment_type</td><td><span class="badge bg-danger">Yes</span></td><td>permanent, contract, part-time</td></tr>
                        <tr><td class="fw-semibold">join_date</td><td><span class="badge bg-danger">Yes</span></td><td>YYYY-MM-DD format</td></tr>
                        <tr><td class="fw-semibold">basic_salary</td><td><span class="badge bg-danger">Yes</span></td><td>Numeric, e.g. 8500.00</td></tr>
                        <tr><td class="fw-semibold">bank_name</td><td><span class="badge bg-secondary">No</span></td><td>e.g. Zanaco</td></tr>
                        <tr><td class="fw-semibold">bank_account</td><td><span class="badge bg-secondary">No</span></td><td>Account number</td></tr>
                        <tr><td class="fw-semibold">sort_code</td><td><span class="badge bg-secondary">No</span></td><td>Branch sort code</td></tr>
                        <tr><td class="fw-semibold">bank_branch</td><td><span class="badge bg-secondary">No</span></td><td>Branch name</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-diagram-3 me-2 text-warning"></i>Department IDs</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" style="font-size:.82rem">
                    <thead class="table-light"><tr><th>ID</th><th>Department Name</th></tr></thead>
                    <tbody>
                        @foreach($departments as $dept)
                        <tr><td><code>{{ $dept->id }}</code></td><td>{{ $dept->name }}</td></tr>
                        @endforeach
                        @if($departments->isEmpty())
                        <tr><td colspan="2" class="text-muted text-center py-3">No departments configured.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const dropZone    = document.getElementById('dropZone');
const fileInput   = document.getElementById('csvFile');
const dropContent = document.getElementById('dropContent');
const fileInfo    = document.getElementById('fileInfo');
const fileName    = document.getElementById('fileName');
const fileSize    = document.getElementById('fileSize');
const uploadBtn   = document.getElementById('uploadBtn');

function showFile(file) {
    if (!file) return;
    dropContent.classList.add('d-none');
    fileInfo.classList.remove('d-none');
    fileName.textContent = file.name;
    fileSize.textContent = file.size < 1024*1024
        ? (file.size/1024).toFixed(1) + ' KB'
        : (file.size/1024/1024).toFixed(1) + ' MB';
    dropZone.style.borderColor = '#198754';
    dropZone.style.background  = '#f0fdf4';
    uploadBtn.disabled = false;
}

fileInput.addEventListener('change', () => showFile(fileInput.files[0]));

['dragover','dragenter'].forEach(e => {
    dropZone.addEventListener(e, ev => {
        ev.preventDefault();
        dropZone.style.borderColor = '#0d6efd';
        dropZone.style.background  = '#f0f4ff';
    });
});
['dragleave','drop'].forEach(e => {
    dropZone.addEventListener(e, ev => {
        ev.preventDefault();
        if (e === 'drop' && ev.dataTransfer.files.length) {
            fileInput.files = ev.dataTransfer.files;
            showFile(ev.dataTransfer.files[0]);
        } else {
            dropZone.style.borderColor = '#dee2e6';
            dropZone.style.background  = '';
        }
    });
});

document.getElementById('uploadForm').addEventListener('submit', function() {
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing…';
});
</script>
@endpush
@endsection
