@extends('layouts.app')
@section('title', 'Scholarships')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Scholarships</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Scholarships</li>
        </ol></nav>
    </div>
    @can('manage-finance')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScholarshipModal">
        <i class="bi bi-plus-circle me-1"></i> New Scholarship
    </button>
    @endcan
</div>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#scholarships-tab">Scholarships</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#awards-tab">Awards</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="scholarships-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th><th>Type</th><th>Coverage</th><th>Max Recipients</th><th>Active Awards</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scholarships as $scholarship)
                        <tr>
                            <td class="fw-semibold">{{ $scholarship->name }}</td>
                            <td>{{ ucfirst($scholarship->type) }}</td>
                            <td>
                                @if($scholarship->coverage_type === 'percentage')
                                    {{ $scholarship->coverage_value }}%
                                @else
                                    {{ formatCurrency($scholarship->coverage_value) }}
                                @endif
                            </td>
                            <td>{{ $scholarship->max_recipients ?? 'Unlimited' }}</td>
                            <td><span class="badge bg-success">{{ $scholarship->awards_count ?? 0 }}</span></td>
                            <td><span class="badge bg-{{ $scholarship->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($scholarship->status) }}</span></td>
                            <td>
                                @can('manage-finance')
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#awardModal" onclick="setAwardScholarship({{ $scholarship->id }}, '{{ addslashes($scholarship->name) }}')">
                                    <i class="bi bi-award me-1"></i> Award
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="editScholarship({{ $scholarship->id }}, '{{ addslashes($scholarship->name) }}', '{{ $scholarship->type }}', '{{ $scholarship->coverage_type }}', '{{ $scholarship->coverage_value }}', '{{ $scholarship->max_recipients }}', '{{ addslashes($scholarship->description ?? '') }}', '{{ $scholarship->status }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="awards-tab">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th><th>Scholarship</th><th>Award Date</th><th>Amount/Coverage</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($awards as $award)
                        <tr>
                            <td>
                                <strong>{{ optional(optional($award->student)->user)->name }}</strong><br>
                                <small class="text-muted">{{ optional($award->student)->student_id }}</small>
                            </td>
                            <td>{{ optional($award->scholarship)->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($award->award_date)->format('d M Y') }}</td>
                            <td>{{ optional($award->scholarship)->coverage_type === 'percentage' ? optional($award->scholarship)->coverage_value . '%' : formatCurrency(optional($award->scholarship)->coverage_value) }}</td>
                            <td>
                                @php $sc = ['active'=>'success','suspended'=>'warning','completed'=>'secondary'] @endphp
                                <span class="badge bg-{{ $sc[$award->status] ?? 'secondary' }}">{{ ucfirst($award->status) }}</span>
                            </td>
                            <td>
                                @can('manage-finance')
                                <form method="POST" action="{{ route('finance.scholarships.revoke', $award) }}" class="d-inline" onsubmit="return confirm('Revoke this scholarship award?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-warning">Revoke</button>
                                </form>
                                <form method="POST" action="{{ route('finance.scholarships.awards.destroy', $award) }}" class="d-inline" onsubmit="return confirm('Permanently delete this award? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete award"><i class="bi bi-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@can('manage-finance')
<div class="modal fade" id="createScholarshipModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('finance.scholarships.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Create Scholarship</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Scholarship Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="merit">Merit-based</option>
                            <option value="need">Need-based</option>
                            <option value="sports">Sports</option>
                            <option value="government">Government</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Coverage Type *</label>
                            <select name="coverage_type" class="form-select">
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Value *</label>
                            <input type="number" name="coverage_value" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Max Recipients</label>
                        <input type="number" name="max_recipients" class="form-control" min="1" placeholder="Leave blank for unlimited">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Scholarship Modal -->
<div class="modal fade" id="editScholarshipModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editScholarshipForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Scholarship</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="es_name" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" id="es_type" class="form-select">
                                <option value="merit">Merit-based</option>
                                <option value="need">Need-based</option>
                                <option value="sports">Sports</option>
                                <option value="government">Government</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="es_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Coverage Type <span class="text-danger">*</span></label>
                            <select name="coverage_type" id="es_coverage_type" class="form-select" required>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Coverage Value <span class="text-danger">*</span></label>
                            <input type="number" name="coverage_value" id="es_coverage_value" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Recipients</label>
                        <input type="number" name="max_recipients" id="es_max_recipients" class="form-control" min="1" placeholder="Leave blank for unlimited">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="es_description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="awardModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('finance.scholarships.award') }}">
            @csrf
            <input type="hidden" name="scholarship_id" id="awardScholarshipId">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Award Scholarship: <span id="awardScholarshipName"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student *</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student</option>
                            @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ optional($s->user)->name }} ({{ $s->student_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Award Date *</label>
                        <input type="date" name="award_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Award Scholarship</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
function setAwardScholarship(id, name) {
    document.getElementById('awardScholarshipId').value = id;
    document.getElementById('awardScholarshipName').textContent = name;
}

function editScholarship(id, name, type, coverageType, coverageValue, maxRecipients, description, status) {
    document.getElementById('es_name').value             = name;
    document.getElementById('es_type').value             = type || 'merit';
    document.getElementById('es_coverage_type').value   = coverageType || 'fixed';
    document.getElementById('es_coverage_value').value  = coverageValue || '';
    document.getElementById('es_max_recipients').value  = maxRecipients || '';
    document.getElementById('es_description').value     = description || '';
    document.getElementById('es_status').value          = status || 'active';
    document.getElementById('editScholarshipForm').action = '/finance/scholarships/' + id;
    new bootstrap.Modal(document.getElementById('editScholarshipModal')).show();
}
</script>
@endpush
@endsection
