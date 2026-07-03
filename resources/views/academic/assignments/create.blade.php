@extends('layouts.app')
@section('title', 'New Assignment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">New Assignment</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.assignments.index') }}">Assignments</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:720px">
    <div class="card-body">
        <form method="POST" action="{{ route('academic.assignments.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Course Offering <span class="text-danger">*</span></label>
                <select name="course_offering_id" class="form-select @error('course_offering_id') is-invalid @enderror" required>
                    <option value="">Select course offering</option>
                    @foreach($offerings as $off)
                    <option value="{{ $off->id }}" {{ old('course_offering_id') == $off->id ? 'selected' : '' }}>
                        {{ optional($off->course)->code }} — {{ optional($off->course)->name }}
                        ({{ optional(optional($off->semester)->academicYear)->name }}, {{ optional($off->semester)->name }})
                    </option>
                    @endforeach
                </select>
                @error('course_offering_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Instructions / Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Describe what students should do...">{{ old('description') }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Due Date &amp; Time <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                    <input type="number" name="total_marks" class="form-control @error('total_marks') is-invalid @enderror" value="{{ old('total_marks', 100) }}" min="1" max="100" required>
                    @error('total_marks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save as Draft</button>
                <a href="{{ route('academic.assignments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
