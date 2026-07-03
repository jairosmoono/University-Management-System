@extends('layouts.app')
@section('title', isset($feeStructure) ? 'Edit Fee Structure' : 'Create Fee Structure')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ isset($feeStructure) ? 'Edit' : 'Create' }} Fee Structure</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.fee-structures.index') }}">Fee Structures</a></li>
            <li class="breadcrumb-item active">{{ isset($feeStructure) ? 'Edit' : 'Create' }}</li>
        </ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($feeStructure) ? route('finance.fee-structures.update', $feeStructure) : route('finance.fee-structures.store') }}">
    @csrf
    @if(isset($feeStructure)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Fee Structure Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $feeStructure->name ?? '') }}" placeholder="e.g. 2024/25 Undergraduate Fees" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Admission Type *</label>
                            <select name="admission_type" class="form-select" required>
                                <option value="full-time" {{ old('admission_type', $feeStructure->admission_type ?? '') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('admission_type', $feeStructure->admission_type ?? '') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="distance"  {{ old('admission_type', $feeStructure->admission_type ?? '') == 'distance'  ? 'selected' : '' }}>Distance</option>
                                <option value="online"    {{ old('admission_type', $feeStructure->admission_type ?? '') == 'online'    ? 'selected' : '' }}>Online</option>
                                <option value="all"       {{ old('admission_type', $feeStructure->admission_type ?? '') == 'all'       ? 'selected' : '' }}>All Types</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program (leave blank for all)</label>
                            <select name="program_id" class="form-select">
                                <option value="">All Programs</option>
                                @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ old('program_id', $feeStructure->program_id ?? '') == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Academic Year *</label>
                            <select name="academic_year_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $feeStructure->academic_year_id ?? '') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Semester/Term</label>
                            <select name="semester_id" class="form-select">
                                <option value="">All Semesters/Terms</option>
                                @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ old('semester_id', $feeStructure->semester_id ?? '') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $feeStructure->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $feeStructure->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="mb-0 fw-semibold">Fee Items</h6>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addFeeItem()">
                        <i class="bi bi-plus me-1"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="feeItems">
                        @if(isset($feeStructure) && $feeStructure->feeItems)
                            @foreach($feeStructure->feeItems as $i => $item)
                            <div class="row g-2 mb-2 fee-item">
                                <div class="col-md-3">
                                    <select name="items[{{ $i }}][fee_type]" class="form-select form-select-sm">
                                        @foreach(['Tuition','Accommodation','Library','Laboratory','Medical','Sports','Technology','Examination','Registration','Other'] as $type)
                                        <option value="{{ $type }}" {{ $item->fee_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="items[{{ $i }}][description]" class="form-control form-control-sm" placeholder="Description" value="{{ $item->description }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[{{ $i }}][amount]" class="form-control form-control-sm amount-input" placeholder="Amount" value="{{ $item->amount }}" min="0" step="0.01" onchange="updateTotal()">
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="items[{{ $i }}][is_mandatory]" value="1" {{ $item->is_mandatory ? 'checked' : '' }}>
                                        <label class="form-check-label small">Req.</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.fee-item').remove(); updateTotal()"><i class="bi bi-x"></i></button>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="row g-2 mb-2 fee-item">
                                <div class="col-md-3">
                                    <select name="items[0][fee_type]" class="form-select form-select-sm">
                                        @foreach(['Tuition','Accommodation','Library','Laboratory','Medical','Sports','Technology','Examination','Registration','Other'] as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="items[0][description]" class="form-control form-control-sm" placeholder="Description">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="items[0][amount]" class="form-control form-control-sm amount-input" placeholder="0.00" min="0" step="0.01" onchange="updateTotal()">
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="items[0][is_mandatory]" value="1" checked>
                                        <label class="form-check-label small">Req.</label>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.fee-item').remove(); updateTotal()"><i class="bi bi-x"></i></button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="text-end mt-3 border-top pt-3">
                        <input type="hidden" name="total_amount" id="totalAmountInput" value="{{ old('total_amount', $feeStructure->total_amount ?? 0) }}">
                        <strong>Total: K <span id="grandTotal">0.00</span></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Actions</h6>
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-save me-1"></i> {{ isset($feeStructure) ? 'Update' : 'Create' }} Fee Structure
                    </button>
                    <a href="{{ route('finance.fee-structures.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
let itemIndex = {{ isset($feeStructure) ? $feeStructure->feeItems?->count() ?? 1 : 1 }};

function addFeeItem() {
    const feeTypes = ['Tuition','Accommodation','Library','Laboratory','Medical','Sports','Technology','Examination','Registration','Other'];
    const opts = feeTypes.map(t => `<option value="${t}">${t}</option>`).join('');
    const html = `
    <div class="row g-2 mb-2 fee-item">
        <div class="col-md-3"><select name="items[${itemIndex}][fee_type]" class="form-select form-select-sm">${opts}</select></div>
        <div class="col-md-4"><input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm" placeholder="Description"></div>
        <div class="col-md-3"><input type="number" name="items[${itemIndex}][amount]" class="form-control form-control-sm amount-input" placeholder="0.00" min="0" step="0.01" onchange="updateTotal()"></div>
        <div class="col-md-1 d-flex align-items-center"><div class="form-check mb-0"><input class="form-check-input" type="checkbox" name="items[${itemIndex}][is_mandatory]" value="1" checked><label class="form-check-label small">Req.</label></div></div>
        <div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.fee-item').remove(); updateTotal()"><i class="bi bi-x"></i></button></div>
    </div>`;
    document.getElementById('feeItems').insertAdjacentHTML('beforeend', html);
    itemIndex++;
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.amount-input').forEach(i => { total += parseFloat(i.value) || 0; });
    document.getElementById('grandTotal').textContent = total.toFixed(2);
    document.getElementById('totalAmountInput').value = total.toFixed(2);
}
updateTotal();
</script>
@endpush
@endsection
