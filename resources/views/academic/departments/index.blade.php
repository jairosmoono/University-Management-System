@extends('layouts.app')
@section('title', 'Departments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Departments</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Departments</li>
        </ol></nav>
    </div>
    @can('manage-academic')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="bi bi-plus-circle me-1"></i> Add Department
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="faculty_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Faculties</option>
                    @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                <a href="{{ route('academic.departments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Department Name</th><th>Code</th><th>Faculty</th><th>HOD</th><th>Programs</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $i => $dept)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $dept->name }}</td>
                    <td><code>{{ $dept->code }}</code></td>
                    <td>{{ optional($dept->faculty)->name }}</td>
                    <td>{{ optional($dept->hod)->name ?? '—' }}</td>
                    <td><span class="badge bg-info">{{ $dept->programs_count ?? 0 }}</span></td>
                    <td>
                        <span class="badge bg-{{ $dept->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($dept->status) }}</span>
                    </td>
                    <td>
                        @can('manage-academic')
                        <button class="btn btn-sm btn-outline-primary" onclick="editDept({{ $dept->id }}, '{{ addslashes($dept->name) }}', '{{ $dept->code }}', {{ $dept->faculty_id }}, {{ $dept->hod_id ?? 'null' }}, '{{ $dept->status }}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('academic.departments.destroy', $dept) }}" class="d-inline" onsubmit="return confirm('Delete this department?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-academic')
<div class="modal fade" id="createDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('academic.departments.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Department</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty *</label>
                        <select name="faculty_id" class="form-select" required>
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Head of Department (HOD)</label>
                        <select name="hod_id" class="form-select">
                            <option value="">— None / To Be Assigned —</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}">
                                {{ optional($s->user)->name ?: 'Staff #'.$s->id }}{{ $s->designation ? ' — '.$s->designation : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editDeptForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Department</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Department Name *</label>
                        <input type="text" name="name" id="dEditName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" id="dEditCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Faculty *</label>
                        <select name="faculty_id" id="dEditFaculty" class="form-select" required>
                            @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Head of Department (HOD)</label>
                        <select name="hod_id" id="dEditHod" class="form-select">
                            <option value="">— None / To Be Assigned —</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}">
                                {{ optional($s->user)->name ?: 'Staff #'.$s->id }}{{ $s->designation ? ' — '.$s->designation : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="dEditStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editDept(id, name, code, facultyId, hodId, status) {
    document.getElementById('dEditName').value    = name;
    document.getElementById('dEditCode').value    = code;
    document.getElementById('dEditFaculty').value = facultyId;
    document.getElementById('dEditHod').value     = hodId ?? '';
    document.getElementById('dEditStatus').value  = status;
    document.getElementById('editDeptForm').action = '/academic/departments/' + id;
    new bootstrap.Modal(document.getElementById('editDeptModal')).show();
}
</script>
@endpush
@endcan
@endsection
