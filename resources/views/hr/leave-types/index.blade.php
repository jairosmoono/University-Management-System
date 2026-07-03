@extends('layouts.app')
@section('title', 'Leave Types')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Leave Types</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hr.leave.index') }}">Leave</a></li>
            <li class="breadcrumb-item active">Leave Types</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeaveTypeModal">
        <i class="bi bi-plus-circle me-1"></i> Add Leave Type
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Days Allowed</th>
                        <th>Paid</th>
                        <th>Description</th>
                        <th>Usage</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveTypes as $lt)
                    <tr>
                        <td class="fw-semibold">{{ $lt->name }}</td>
                        <td><span class="badge bg-primary">{{ $lt->days_allowed }} days</span></td>
                        <td>
                            @if($lt->is_paid)
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-secondary">Unpaid</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $lt->description ?: '—' }}</td>
                        <td>{{ $lt->leave_requests_count }} requests</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick="openEditModal({{ $lt->id }}, '{{ addslashes($lt->name) }}', {{ $lt->days_allowed }}, {{ $lt->is_paid ? 1 : 0 }}, '{{ addslashes($lt->description ?? '') }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('hr.leave-types.destroy', $lt) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this leave type?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">No leave types defined yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addLeaveTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hr.leave-types.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Leave Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="100">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Days Allowed <span class="text-danger">*</span></label>
                            <input type="number" name="days_allowed" class="form-control" required min="1" max="365" value="1">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_paid" value="1" id="add_is_paid" checked>
                                <label class="form-check-label" for="add_is_paid">Paid Leave</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editLeaveTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editLeaveTypeForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Leave Type</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required maxlength="100">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Days Allowed <span class="text-danger">*</span></label>
                            <input type="number" name="days_allowed" id="edit_days_allowed" class="form-control" required min="1" max="365">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_paid" value="1" id="edit_is_paid">
                                <label class="form-check-label" for="edit_is_paid">Paid Leave</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id, name, days, isPaid, desc) {
    document.getElementById('editLeaveTypeForm').action = '/hr/leave-types/' + id;
    document.getElementById('edit_name').value          = name;
    document.getElementById('edit_days_allowed').value  = days;
    document.getElementById('edit_is_paid').checked     = isPaid == 1;
    document.getElementById('edit_description').value   = desc;
    new bootstrap.Modal(document.getElementById('editLeaveTypeModal')).show();
}
</script>
@endpush
@endsection
