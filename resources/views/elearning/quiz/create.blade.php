@extends('layouts.app')
@section('title', 'Create Quiz')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Create Quiz</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('elearning.index') }}">E-Learning</a></li>
        <li class="breadcrumb-item"><a href="{{ route('elearning.show', $eLearningCourse) }}">{{ $eLearningCourse->courseOffering->course?->name }}</a></li>
        <li class="breadcrumb-item active">New Quiz</li>
    </ol></nav>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                <h6 class="fw-semibold mb-0">Quiz Details</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <form method="POST" action="{{ route('elearning.quizzes.store', $eLearningCourse) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Quiz Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="e.g. Chapter 1 Assessment" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instructions</label>
                        <textarea name="description" rows="3" class="form-control"
                            placeholder="Instructions or notes for students…">{{ old('description') }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Passing Score (%) <span class="text-danger">*</span></label>
                            <input type="number" name="passing_score" class="form-control" value="{{ old('passing_score', 50) }}"
                                   min="1" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Max Attempts <span class="text-danger">*</span></label>
                            <input type="number" name="max_attempts" class="form-control" value="{{ old('max_attempts', 3) }}"
                                   min="1" max="10" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Time Limit (min)</label>
                            <input type="number" name="time_limit_minutes" class="form-control"
                                   value="{{ old('time_limit_minutes') }}" min="1" max="300" placeholder="No limit">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                   id="qpub" {{ old('is_published') ? 'checked' : '' }}>
                            <label class="form-check-label" for="qpub">Publish immediately</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-patch-question me-1"></i>Create Quiz
                        </button>
                        <a href="{{ route('elearning.show', $eLearningCourse) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
