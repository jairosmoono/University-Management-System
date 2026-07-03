@extends('layouts.app')
@section('title', 'Edit Fee Structure')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Fee Structure</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.fee-structures.index') }}">Fee Structures</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('finance.fee-structures.update', $feeStructure) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $feeStructure->name) }}" required>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['tuition','accommodation','registration','library','medical','activity','other'] as $t)
                            <option value="{{ $t }}" {{ old('type', $feeStructure->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">ZMW</span>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount', $feeStructure->amount) }}" step="0.01" min="0" required>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                <select name="academic_year_id" class="form-select" required>
                    <option value="">— Select —</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ old('academic_year_id', $feeStructure->academic_year_id) == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Semester/Term <small class="text-muted">(optional)</small></label>
                    <select name="semester_id" class="form-select">
                        <option value="">All Semesters/Terms</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ old('semester_id', $feeStructure->semester_id) == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Program <small class="text-muted">(optional)</small></label>
                    <select name="program_id" class="form-select">
                        <option value="">All Programs</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" {{ old('program_id', $feeStructure->program_id) == $prog->id ? 'selected' : '' }}>{{ $prog->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Fee Structure</button>
                <a href="{{ route('finance.fee-structures.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
