@extends('layouts.app')
@section('title', 'Edit Scholarship')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Scholarship</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.scholarships.index') }}">Scholarships</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('finance.scholarships.update', $scholarship) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Scholarship Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $scholarship->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['merit' => 'Merit-based', 'need' => 'Need-based', 'sports' => 'Sports', 'government' => 'Government', 'other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $scholarship->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ old('status', $scholarship->status) === 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $scholarship->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Coverage Type <span class="text-danger">*</span></label>
                    <select name="coverage_type" class="form-select @error('coverage_type') is-invalid @enderror" required>
                        <option value="percentage" {{ old('coverage_type', $scholarship->coverage_type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed"      {{ old('coverage_type', $scholarship->coverage_type) === 'fixed'      ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                    @error('coverage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Coverage Value <span class="text-danger">*</span></label>
                    <input type="number" name="coverage_value" class="form-control @error('coverage_value') is-invalid @enderror"
                           value="{{ old('coverage_value', $scholarship->coverage_value) }}" min="0" step="0.01" required>
                    @error('coverage_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Max Recipients</label>
                <input type="number" name="max_recipients" class="form-control"
                       value="{{ old('max_recipients', $scholarship->max_recipients) }}" min="1"
                       placeholder="Leave blank for unlimited">
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $scholarship->description) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update Scholarship</button>
                <a href="{{ route('finance.scholarships.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
