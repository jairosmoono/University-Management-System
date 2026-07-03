@extends('layouts.app')
@section('title', 'Create E-Learning Course')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Create E-Learning Course</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Course Setup</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('elearning.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course Offering <span class="text-danger">*</span></label>
                        @if($offerings->isEmpty())
                        <div class="alert alert-warning small">All your course offerings already have e-learning content, or you have no assigned offerings.</div>
                        @else
                        <select name="course_offering_id" class="form-select @error('course_offering_id') is-invalid @enderror" required>
                            <option value="">Select a course offering…</option>
                            @foreach($offerings as $offering)
                            <option value="{{ $offering->id }}"
                                {{ (old('course_offering_id') ?? request('offering_id')) == $offering->id ? 'selected' : '' }}>
                                {{ $offering->course?->name }} ({{ $offering->course?->code }}) — {{ $offering->semester?->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('course_offering_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Course Description</label>
                        <textarea name="description" rows="4" class="form-control"
                            placeholder="Describe what students will learn in this course…">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1" id="publishCheck"
                                {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="publishCheck">Publish immediately (visible to students)</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" {{ $offerings->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-laptop me-1"></i>Create E-Learning Course
                        </button>
                        <a href="{{ route('elearning.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
