@extends('layouts.app')
@section('title', 'Fee Structures')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Fee Structures</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Fee Structures</li>
        </ol></nav>
    </div>
    @can('manage-finance')
    <a href="{{ route('finance.fee-structures.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> New Fee Structure
    </a>
    @endcan
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th><th>Program</th><th>Academic Year</th><th>Student Type</th><th>Items</th><th>Total Amount</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feeStructures as $fs)
                <tr>
                    <td class="fw-semibold">{{ $fs->name }}</td>
                    <td>{{ optional($fs->program)->name ?? 'All Programs' }}</td>
                    <td>{{ optional($fs->academicYear)->name }}</td>
                    <td><span class="badge bg-info">{{ ucfirst(str_replace('-', ' ', $fs->admission_type)) }}</span></td>
                    <td>{{ $fs->items_count ?? $fs->feeItems?->count() }}</td>
                    <td class="fw-semibold">{{ formatCurrency($fs->total_amount) }}</td>
                    <td><span class="badge bg-{{ $fs->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($fs->status) }}</span></td>
                    <td>
                        @can('manage-finance')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Actions</button>
                            <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="openEditModal({{ $fs->id }}); return false;"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                <li>
                                    <form method="POST" action="{{ route('finance.fee-structures.clone', $fs) }}">
                                        @csrf
                                        <button class="dropdown-item"><i class="bi bi-copy me-2"></i>Clone</button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('finance.fee-structures.destroy', $fs) }}" onsubmit="return confirm('Delete fee structure?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@can('manage-finance')
{{-- Edit Fee Structure Modal --}}
<div class="modal fade" id="editFsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form method="POST" id="editFsForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editFsLoading" class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                        <p class="mt-2 text-muted">Loading…</p>
                    </div>
                    <div id="editFsBody" style="display:none">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                {{-- Basic Info --}}
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-transparent border-0 py-3">
                                        <h6 class="mb-0 fw-semibold">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label">Fee Structure Name *</label>
                                                <input type="text" name="name" id="ef_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Admission Type *</label>
                                                <select name="admission_type" id="ef_admission_type" class="form-select" required>
                                                    <option value="full-time">Full-Time</option>
                                                    <option value="part-time">Part-Time</option>
                                                    <option value="distance">Distance</option>
                                                    <option value="online">Online</option>
                                                    <option value="all">All Types</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Program</label>
                                                <select name="program_id" id="ef_program_id" class="form-select">
                                                    <option value="">All Programs</option>
                                                    @foreach($programs as $prog)
                                                    <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Academic Year *</label>
                                                <select name="academic_year_id" id="ef_academic_year_id" class="form-select" required>
                                                    <option value="">Select</option>
                                                    @foreach($academicYears as $year)
                                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Semester/Term</label>
                                                <select name="semester_id" id="ef_semester_id" class="form-select">
                                                    <option value="">All Semesters/Terms</option>
                                                    @foreach($semesters as $sem)
                                                    <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Status</label>
                                                <select name="status" id="ef_status" class="form-select">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fee Items --}}
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                                        <h6 class="mb-0 fw-semibold">Fee Items</h6>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="efAddItem()">
                                            <i class="bi bi-plus me-1"></i> Add Item
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="efFeeItems"></div>
                                        <div class="text-end mt-3 border-top pt-3">
                                            <input type="hidden" name="total_amount" id="ef_total_amount" value="0">
                                            <strong>Total: K <span id="efGrandTotal">0.00</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-semibold mb-3">Actions</h6>
                                        <button type="submit" class="btn btn-primary w-100 mb-2">
                                            <i class="bi bi-save me-1"></i> Update Fee Structure
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
const EF_FEE_TYPES = ['Tuition','Accommodation','Library','Laboratory','Medical','Sports','Technology','Examination','Registration','Other'];
let efItemIndex = 0;

function efAddItem(item) {
    const opts = EF_FEE_TYPES.map(t => `<option value="${t}" ${item && item.fee_type === t ? 'selected' : ''}>${t}</option>`).join('');
    const html = `
    <div class="row g-2 mb-2 ef-item">
        <div class="col-md-3"><select name="items[${efItemIndex}][fee_type]" class="form-select form-select-sm">${opts}</select></div>
        <div class="col-md-4"><input type="text" name="items[${efItemIndex}][description]" class="form-control form-control-sm" placeholder="Description" value="${item ? (item.description || '') : ''}"></div>
        <div class="col-md-3"><input type="number" name="items[${efItemIndex}][amount]" class="form-control form-control-sm ef-amount" placeholder="0.00" min="0" step="0.01" value="${item ? (item.amount || '') : ''}" oninput="efUpdateTotal()" onchange="efUpdateTotal()"></div>
        <div class="col-md-1 d-flex align-items-center">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="items[${efItemIndex}][is_mandatory]" value="1" ${!item || item.is_mandatory ? 'checked' : ''}>
                <label class="form-check-label small">Req.</label>
            </div>
        </div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.ef-item').remove(); efUpdateTotal()"><i class="bi bi-x"></i></button></div>
    </div>`;
    document.getElementById('efFeeItems').insertAdjacentHTML('beforeend', html);
    efItemIndex++;
}

function efUpdateTotal() {
    let total = 0;
    document.querySelectorAll('.ef-amount').forEach(i => { total += parseFloat(i.value) || 0; });
    document.getElementById('efGrandTotal').textContent = total.toFixed(2);
    document.getElementById('ef_total_amount').value = total.toFixed(2);
}

function openEditModal(id) {
    const modal   = new bootstrap.Modal(document.getElementById('editFsModal'));
    const loading = document.getElementById('editFsLoading');
    const body    = document.getElementById('editFsBody');

    loading.style.display = 'block';
    body.style.display    = 'none';
    document.getElementById('efFeeItems').innerHTML = '';
    efItemIndex = 0;
    modal.show();

    fetch(`/finance/fee-structures/${id}/json`)
        .then(r => r.json())
        .then(fs => {
            document.getElementById('editFsForm').action = `/finance/fee-structures/${id}`;
            document.getElementById('ef_name').value             = fs.name || '';
            document.getElementById('ef_admission_type').value    = fs.admission_type || 'full-time';
            document.getElementById('ef_program_id').value       = fs.program_id || '';
            document.getElementById('ef_academic_year_id').value = fs.academic_year_id || '';
            document.getElementById('ef_semester_id').value      = fs.semester_id || '';
            document.getElementById('ef_status').value           = fs.status || 'active';

            (fs.fee_items || []).forEach(item => efAddItem(item));
            if (!fs.fee_items || !fs.fee_items.length) efAddItem(null);
            efUpdateTotal();

            loading.style.display = 'none';
            body.style.display    = 'block';
        })
        .catch(() => {
            loading.innerHTML = '<div class="text-danger">Failed to load fee structure.</div>';
        });
}
</script>
@endpush

@endsection
