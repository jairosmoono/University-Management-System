@extends('layouts.app')
@section('title', 'Edit Assignment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Assignment</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.assignments.index') }}">Assignments</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('academic.assignments.update', $assignment) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Course Offering</label>
                <input type="text" class="form-control" value="{{ optional(optional($assignment->courseOffering)->course)->code }} — {{ optional(optional($assignment->courseOffering)->course)->name }}" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $assignment->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Instructions / Description</label>
                <textarea name="description" class="form-control" rows="5">{{ old('description', $assignment->description) }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Due Date &amp; Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror"
                        value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}" required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                    <input type="number" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', $assignment->total_marks) }}" min="1" max="100" required>
                    @error('total_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Assignment</button>
                <a href="{{ route('academic.assignments.show', $assignment) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
