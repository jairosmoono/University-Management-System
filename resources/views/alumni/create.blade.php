@extends('layouts.app')
@section('title', 'Register Alumni')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Register Alumni</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('alumni.index') }}">Alumni</a></li>
            <li class="breadcrumb-item active">Register</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('alumni.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Student <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">— Select Student —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }} ({{ $student->student_number }})</option>
                    @endforeach
                </select>
                @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Graduation Year <span class="text-danger">*</span></label>
                    <input type="number" name="graduation_year" class="form-control" value="{{ old('graduation_year', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Employment Status</label>
                    <select name="employment_status" class="form-select">
                        @foreach(['employed','unemployed','self_employed','further_studies','unknown'] as $s)
                            <option value="{{ $s }}" {{ old('employment_status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Current Employer</label>
                <input type="text" name="current_employer" class="form-control" value="{{ old('current_employer') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin_url" class="form-control" value="{{ old('linkedin_url') }}">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Register Alumni</button>
                <a href="{{ route('alumni.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
