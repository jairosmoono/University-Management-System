@extends('layouts.app')
@section('title', 'Employment Listings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Employment Listings</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Employment Listings</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addListingModal">
        <i class="bi bi-plus-circle me-1"></i> Post Job
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([['Total', $stats['total'], 'primary', 'briefcase'], ['Open', $stats['open'], 'success', 'door-open'], ['Closed', $stats['closed'], 'secondary', 'door-closed'], ['Draft', $stats['draft'], 'warning', 'pencil-square']] as [$label, $count, $color, $icon])
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 bg-{{ $color }} bg-opacity-10 p-3">
                    <i class="bi bi-{{ $icon }} text-{{ $color }} fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-3 lh-1">{{ $count }}</div>
                    <div class="text-muted small mt-1">{{ $label }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="open" {{ request('status')=='open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ request('status')=='closed' ? 'selected' : '' }}>Closed</option>
                    <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id')==$dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->anyFilled(['status','department_id']))
            <div class="col-auto"><a href="{{ route('hr.employment-listings.index') }}" class="btn btn-sm btn-light">Clear</a></div>
            @endif
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Department</th><th>Type</th><th>Vacancies</th><th>Deadline</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($listings as $listing)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $listing->title }}</div>
                            @if($listing->description)
                            <div class="text-muted small">{{ \Illuminate\Support\Str::limit($listing->description, 60) }}</div>
                            @endif
                        </td>
                        <td>{{ optional($listing->department)->name ?? 'Any' }}</td>
                        <td><span class="badge bg-info">{{ ucfirst(str_replace('-', ' ', $listing->employment_type)) }}</span></td>
                        <td>{{ $listing->vacancies }}</td>
                        <td>
                            @if($listing->deadline)
                                @php $expired = $listing->deadline->isPast(); @endphp
                                <span class="{{ $expired ? 'text-danger' : 'text-dark' }}">
                                    {{ $listing->deadline->format('d M Y') }}
                                    @if($expired) <small>(Expired)</small> @endif
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $listing->status === 'open' ? 'success' : ($listing->status === 'draft' ? 'warning text-dark' : 'secondary') }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1" onclick="openEditListing({{ $listing->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('hr.employment-listings.destroy', $listing) }}" class="d-inline"
                                  onsubmit="return confirm('Delete this listing?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No job listings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $listings->withQueryString()->links() }}

{{-- Add Modal --}}
<div class="modal fade" id="addListingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('hr.employment-listings.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Post New Job</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    @include('hr.employment-listings._form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post Job</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editListingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editListingForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Job Listing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editListingLoading" class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                    </div>
                    <div id="editListingBody" style="display:none">
                        @include('hr.employment-listings._form', ['prefix' => 'e_'])
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
const listings = @json($listings->keyBy('id'));

function openEditListing(id) {
    const l = listings[id];
    if (!l) return;

    document.getElementById('editListingForm').action = '/hr/employment-listings/' + id;
    document.getElementById('e_title').value           = l.title || '';
    document.getElementById('e_department_id').value  = l.department_id || '';
    document.getElementById('e_employment_type').value = l.employment_type || 'full-time';
    document.getElementById('e_vacancies').value       = l.vacancies || 1;
    document.getElementById('e_deadline').value        = l.deadline || '';
    document.getElementById('e_status').value          = l.status || 'open';
    document.getElementById('e_description').value     = l.description || '';
    document.getElementById('e_requirements').value    = l.requirements || '';

    document.getElementById('editListingLoading').style.display = 'none';
    document.getElementById('editListingBody').style.display    = 'block';
    new bootstrap.Modal(document.getElementById('editListingModal')).show();
}
</script>
@endpush
@endsection
