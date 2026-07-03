@extends('layouts.app')
@section('title', 'Add Timetable Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Add Timetable Entry</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.timetable.index') }}">Timetable</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('academic.timetable.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Course Offering <span class="text-danger">*</span></label>
                <select name="course_offering_id" class="form-select @error('course_offering_id') is-invalid @enderror" required>
                    <option value="">— Select Course Offering —</option>
                    @foreach($offerings as $off)
                        <option value="{{ $off->id }}" {{ old('course_offering_id') == $off->id ? 'selected' : '' }}>{{ optional($off->course)->code }} — {{ optional($off->course)->name }} ({{ optional($off->lecturer)->user->name ?? 'No Lecturer' }})</option>
                    @endforeach
                </select>
                @error('course_offering_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Day <span class="text-danger">*</span></label>
                <select name="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                    <option value="">— Select Day —</option>
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                        <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                    @endforeach
                </select>
                @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Room</label>
                    <input type="text" name="room" class="form-control" value="{{ old('room') }}" placeholder="e.g. LH-01">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['lecture','tutorial','lab','seminar'] as $t)
                            <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Entry</button>
                <a href="{{ route('academic.timetable.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
