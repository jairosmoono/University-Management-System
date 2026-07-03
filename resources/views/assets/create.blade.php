@extends('layouts.app')
@section('title', isset($asset) ? 'Edit Asset' : 'Add Asset')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ isset($asset) ? 'Edit Asset' : 'Add New Asset' }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
            <li class="breadcrumb-item active">{{ isset($asset) ? 'Edit' : 'New' }}</li>
        </ol></nav>
    </div>
</div>

<form method="POST" action="{{ isset($asset) ? route('assets.update', $asset) : route('assets.store') }}">
    @csrf
    @if(isset($asset)) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Asset Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Asset Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $asset->name ?? '') }}" required placeholder="e.g., Dell Latitude Laptop">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asset Code *</label>
                            <input type="text" name="asset_code" class="form-control @error('asset_code') is-invalid @enderror"
                                value="{{ old('asset_code', $asset->asset_code ?? '') }}" required placeholder="e.g., AST-2025-001">
                            @error('asset_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach(['Electronics','Furniture','Vehicle','Machinery','Lab Equipment','Office Equipment','Building','Land','Other'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $asset->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">-- No Department --</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $asset->department_id ?? '') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control"
                                value="{{ old('serial_number', $asset->serial_number ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location / Room</label>
                            <input type="text" name="location" class="form-control"
                                value="{{ old('location', $asset->location ?? '') }}" placeholder="e.g., Room 201, Block A">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Specifications, model, etc.">{{ old('description', $asset->description ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Financial Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control"
                                value="{{ old('purchase_date', isset($asset) ? $asset->purchase_date?->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price (K)</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="purchase_price" class="form-control"
                                    value="{{ old('purchase_price', $asset->purchase_price ?? '') }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Current Book Value (K)</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="current_value" class="form-control"
                                    value="{{ old('current_value', $asset->current_value ?? '') }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Warranty Expiry</label>
                            <input type="date" name="warranty_expiry" class="form-control"
                                value="{{ old('warranty_expiry', isset($asset) ? $asset->warranty_expiry?->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Depreciation Settings --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-down-arrow me-2 text-warning"></i>Depreciation Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Depreciation Method</label>
                            <select name="depreciation_method" id="dep_method" class="form-select" onchange="toggleDepFields()">
                                <option value="">— None (no auto-depreciation) —</option>
                                <option value="straight_line"
                                    {{ old('depreciation_method', $asset->depreciation_method ?? '') === 'straight_line' ? 'selected' : '' }}>
                                    Straight-Line
                                </option>
                                <option value="declining_balance"
                                    {{ old('depreciation_method', $asset->depreciation_method ?? '') === 'declining_balance' ? 'selected' : '' }}>
                                    Declining Balance
                                </option>
                            </select>
                            <div class="form-text">Straight-line: equal amount each year. Declining balance: % of remaining value.</div>
                        </div>
                        <div class="col-md-3" id="field_useful_life">
                            <label class="form-label">Useful Life (years)</label>
                            <input type="number" name="useful_life_years" class="form-control"
                                value="{{ old('useful_life_years', $asset->useful_life_years ?? '') }}" min="1" max="100" placeholder="e.g. 5">
                        </div>
                        <div class="col-md-3" id="field_dep_rate">
                            <label class="form-label">Annual Rate (%)</label>
                            <div class="input-group">
                                <input type="number" name="depreciation_rate" class="form-control"
                                    value="{{ old('depreciation_rate', $asset->depreciation_rate ?? '') }}" min="0" max="100" step="0.01" placeholder="e.g. 20">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salvage / Residual Value (K)</label>
                            <div class="input-group">
                                <span class="input-group-text">K</span>
                                <input type="number" name="salvage_value" class="form-control"
                                    value="{{ old('salvage_value', $asset->salvage_value ?? '0') }}" min="0" step="0.01">
                            </div>
                            <div class="form-text">Minimum value — depreciation stops here.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3 sticky-top" style="top: 80px">
                <div class="card-header bg-transparent border-0 py-3">
                    <h6 class="mb-0 fw-semibold">Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Asset Status</label>
                        <select name="status" class="form-select">
                            @foreach(['active'=>'Active','maintenance'=>'Under Maintenance','disposed'=>'Disposed','lost'=>'Lost/Stolen'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $asset->status ?? 'active') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> {{ isset($asset) ? 'Update Asset' : 'Save Asset' }}
                        </button>
                        <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('scripts')
<script>
function toggleDepFields() {
    const method = document.getElementById('dep_method').value;
    const lifeField = document.getElementById('field_useful_life');
    const rateField = document.getElementById('field_dep_rate');
    if (method === 'straight_line') {
        lifeField.style.opacity = '1';
        rateField.style.opacity = '0.4';
        lifeField.querySelector('input').required = true;
    } else if (method === 'declining_balance') {
        lifeField.style.opacity = '0.4';
        rateField.style.opacity = '1';
        lifeField.querySelector('input').required = false;
    } else {
        lifeField.style.opacity = '0.4';
        rateField.style.opacity = '0.4';
        lifeField.querySelector('input').required = false;
    }
}
document.addEventListener('DOMContentLoaded', toggleDepFields);
</script>
@endpush
@endsection
