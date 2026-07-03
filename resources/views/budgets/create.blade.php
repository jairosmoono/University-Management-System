@extends('layouts.app')
@section('title', 'New Department Budget')
@section('page-title', 'New Department Budget')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-wallet2 me-2" style="color:var(--secondary)"></i>Create Budget</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('academic.budgets.index') }}">Budgets</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header py-3"><h5 class="mb-0 fw-semibold">Budget Details</h5></div>
    <div class="card-body">
        <form action="{{ route('academic.budgets.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                    <option value="">— Select department —</option>
                    @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Academic Year <span class="text-danger">*</span></label>
                <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                    <option value="">— Select academic year —</option>
                    @foreach($academicYears as $y)
                    <option value="{{ $y->id }}" {{ old('academic_year_id') == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                    @endforeach
                </select>
                @error('academic_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Fiscal Year Label <span class="text-danger">*</span></label>
                <input type="text" name="fiscal_year" class="form-control @error('fiscal_year') is-invalid @enderror"
                       placeholder="e.g. 2025/2026" value="{{ old('fiscal_year') }}" required>
                @error('fiscal_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Total Budget (K) <span class="text-danger">*</span></label>
                <input type="number" name="total_budget" class="form-control @error('total_budget') is-invalid @enderror"
                       placeholder="0.00" step="0.01" min="0" value="{{ old('total_budget') }}" required>
                @error('total_budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Optional notes…">{{ old('description') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Create Budget</button>
                <a href="{{ route('academic.budgets.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
