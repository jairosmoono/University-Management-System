@extends('layouts.app')
@section('title', 'Add Course Offering')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Course Offering</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.course-offerings.index') }}">Course Offerings</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('academic.course-offerings.store') }}" method="POST">
            @csrf
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="mb-3">
                <label class="form-label">Course <span class="text-danger">*</span></label>
                <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                    <option value="">— Select Course —</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->code }} — {{ $course->name }}</option>
                    @endforeach
                </select>
                @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Semester/Term <span class="text-danger">*</span></label>
                <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                    <option value="">— Select Semester/Term —</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}
                            {{ $sem->is_current ? 'selected' : '' }}>
                            {{ $sem->name }} — {{ optional($sem->academicYear)->name }}{{ $sem->is_current ? ' (Current)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('semester_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Lecturer</label>
                <select name="lecturer_id" class="form-select">
                    <option value="">— No Lecturer Assigned —</option>
                    @foreach($lecturers as $lec)
                        <option value="{{ $lec->id }}" {{ old('lecturer_id') == $lec->id ? 'selected' : '' }}>{{ $lec->full_name ?? optional($lec->user)->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Max Students <span class="text-danger">*</span></label>
                    <input type="number" name="max_students" class="form-control @error('max_students') is-invalid @enderror" value="{{ old('max_students', 50) }}" min="1" required>
                    @error('max_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Venue / Room</label>
                    <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="e.g. LH-01">
                </div>
                <div class="col-12">
                    <label class="form-label">Schedule</label>
                    <input type="text" name="schedule" class="form-control" value="{{ old('schedule') }}" placeholder="e.g. Mon/Wed 08:00-10:00">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Offering</button>
                <a href="{{ route('academic.course-offerings.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
