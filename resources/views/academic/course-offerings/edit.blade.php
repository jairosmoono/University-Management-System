@extends('layouts.app')
@section('title', 'Edit Course Offering')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Course Offering</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.course-offerings.index') }}">Course Offerings</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('academic.course-offerings.update', $courseOffering) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Course <span class="text-danger">*</span></label>
                <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                    <option value="">— Select Course —</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $courseOffering->course_id) == $course->id ? 'selected' : '' }}>{{ $course->code }} — {{ $course->name }}</option>
                    @endforeach
                </select>
                @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Semester/Term <span class="text-danger">*</span></label>
                <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                    <option value="">— Select Semester/Term —</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ old('semester_id', $courseOffering->semester_id) == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                    @endforeach
                </select>
                @error('semester_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Lecturer</label>
                <select name="lecturer_id" class="form-select">
                    <option value="">— No Lecturer Assigned —</option>
                    @foreach($lecturers as $lec)
                        <option value="{{ $lec->id }}" {{ old('lecturer_id', $courseOffering->lecturer_id) == $lec->id ? 'selected' : '' }}>{{ $lec->full_name ?? optional($lec->user)->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Max Students</label>
                    <input type="number" name="max_students" class="form-control" value="{{ old('max_students', $courseOffering->max_students) }}" min="1">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Room</label>
                    <input type="text" name="room" class="form-control" value="{{ old('room', $courseOffering->room) }}">
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Offering</button>
                <a href="{{ route('academic.course-offerings.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
