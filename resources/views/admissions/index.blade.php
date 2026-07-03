@extends('layouts.app')
@section('title', 'Admissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Admissions Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Admissions</li>
        </ol></nav>
    </div>
    @can('manage-admissions')
    <a href="{{ route('admissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> New Application
    </a>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-primary fw-bold">{{ $stats['total'] }}</h4>
            <small class="text-muted">Total Applications</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-warning fw-bold">{{ $stats['pending'] }}</h4>
            <small class="text-muted">Pending Review</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-success fw-bold">{{ $stats['approved'] }}</h4>
            <small class="text-muted">Approved</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <h4 class="text-danger fw-bold">{{ $stats['rejected'] }}</h4>
            <small class="text-muted">Rejected</small>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                @php $semesters = \App\Models\Semester::orderBy('name')->get(); @endphp
                <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="waitlisted" {{ request('status') == 'waitlisted' ? 'selected' : '' }}>Waitlisted</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name/number..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Search</button>
                <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>App. No.</th><th>Applicant Name</th><th>Program</th><th>Date Applied</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admissions as $admission)
                <tr>
                    <td><code>{{ $admission->application_number }}</code></td>
                    <td class="fw-semibold">{{ $admission->full_name }}</td>
                    <td>{{ optional($admission->program)->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($admission->created_at)->format('d M Y') }}</td>
                    <td>
                        @php $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','waitlisted'=>'info'] @endphp
                        <span class="badge bg-{{ $sc[$admission->status] ?? 'secondary' }}">{{ ucfirst($admission->status) }}</span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admissions.show', $admission) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                @can('manage-admissions')
                                @if($admission->status === 'pending')
                                <li>
                                    <form method="POST" action="{{ route('admissions.approve', $admission) }}">
                                        @csrf
                                        <button class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Approve</button>
                                    </form>
                                </li>
                                <li>
                                    <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="setReject({{ $admission->id }})">
                                        <i class="bi bi-x-circle me-2"></i>Reject
                                    </button>
                                </li>
                                @endif
                                @if($admission->status === 'approved')
                                <li><a class="dropdown-item" href="{{ route('admissions.letter', $admission) }}"><i class="bi bi-file-pdf me-2"></i>Admission Letter</a></li>
                                @endif
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $admissions->withQueryString()->links() }}
    </div>
</div>

@can('manage-admissions')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="rejectForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-danger">Reject Application</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Please provide a clear reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
function setReject(id) {
    document.getElementById('rejectForm').action = '/admissions/' + id + '/reject';
}
</script>
@endpush
@endsection
