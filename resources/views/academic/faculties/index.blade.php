@extends('layouts.app')
@section('title', 'Faculties')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Faculties</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Faculties</li>
            </ol>
        </nav>
    </div>
    @can('manage-academic')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFacultyModal">
        <i class="bi bi-plus-circle me-1"></i> Add Faculty
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="text-primary fw-bold">{{ $faculties->count() }}</h3>
            <small class="text-muted">Total Faculties</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h3 class="text-success fw-bold">{{ $faculties->where('status','active')->count() }}</h3>
            <small class="text-muted">Active</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Faculty Name</th>
                    <th>Code</th>
                    <th>Dean</th>
                    <th>Departments</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($faculties as $i => $faculty)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-semibold">{{ $faculty->name }}</td>
                    <td><code>{{ $faculty->code }}</code></td>
                    <td>{{ $faculty->dean?->user?->name ?? '—' }}</td>
                    <td><span class="badge bg-info">{{ $faculty->departments_count ?? 0 }}</span></td>
                    <td>
                        @if($faculty->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @can('manage-academic')
                        <button class="btn btn-sm btn-outline-primary" onclick="editFaculty({{ $faculty->id }}, '{{ addslashes($faculty->name) }}', '{{ $faculty->code }}', '{{ $faculty->status }}', '{{ addslashes($faculty->description) }}', {{ $faculty->dean_id ?? 'null' }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="{{ route('academic.faculties.destroy', $faculty) }}" class="d-inline" onsubmit="return confirm('Delete this faculty?')">
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
<!-- Create Modal -->
<div class="modal fade" id="createFacultyModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('academic.faculties.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Faculty Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. FOE" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dean</label>
                        <select name="dean_id" class="form-select">
                            <option value="">— Select Dean —</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->user->name }}</option>
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
                    <button type="submit" class="btn btn-primary">Save Faculty</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editFacultyModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editFacultyForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Faculty</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Faculty Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="editCode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dean</label>
                        <select name="dean_id" id="editDeanId" class="form-select">
                            <option value="">— Select Dean —</option>
                            @foreach($staff as $s)
                            <option value="{{ $s->id }}">{{ $s->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="editDesc" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Faculty</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function editFaculty(id, name, code, status, desc, deanId) {
    document.getElementById('editName').value   = name;
    document.getElementById('editCode').value   = code;
    document.getElementById('editStatus').value = status;
    document.getElementById('editDesc').value   = desc;
    const deanSel = document.getElementById('editDeanId');
    deanSel.value = deanId ?? '';
    document.getElementById('editFacultyForm').action = '/academic/faculties/' + id;
    new bootstrap.Modal(document.getElementById('editFacultyModal')).show();
}
</script>
@endpush
@endcan
@endsection
