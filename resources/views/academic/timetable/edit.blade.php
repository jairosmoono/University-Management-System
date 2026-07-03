@extends('layouts.app')
@section('title', 'Edit Timetable Entry')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit Timetable Entry</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.timetable.index') }}">Timetable</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
    </div>
</div>
<div class="card border-0 shadow-sm" style="max-width:600px">
    <div class="card-body">
        <form action="{{ route('academic.timetable.update', $timetable) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Course Offering</label>
                <select name="course_offering_id" class="form-select">
                    <option value="">— Select —</option>
                    @foreach($offerings as $off)
                        <option value="{{ $off->id }}" {{ old('course_offering_id', $timetable->course_offering_id) == $off->id ? 'selected' : '' }}>{{ optional($off->course)->code }} — {{ optional($off->course)->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Day</label>
                <select name="day_of_week" class="form-select">
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday'] as $day)
                        <option value="{{ $day }}" {{ old('day_of_week', $timetable->day_of_week) === $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $timetable->start_time) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $timetable->end_time) }}">
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Room</label>
                    <input type="text" name="room" class="form-control" value="{{ old('room', $timetable->room) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        @foreach(['lecture','tutorial','lab','seminar'] as $t)
                            <option value="{{ $t }}" {{ old('type', $timetable->type) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Entry</button>
                <a href="{{ route('academic.timetable.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
