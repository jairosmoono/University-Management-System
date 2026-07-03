@extends('layouts.app')
@section('title', 'Schedule Examination')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Schedule Examination</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('academic.examinations.index') }}">Examinations</a></li>
            <li class="breadcrumb-item active">Schedule</li>
        </ol></nav>
    </div>
</div>

<div class="card border-0 shadow-sm" style="max-width:750px">
    <div class="card-body">
        <form action="{{ route('academic.examinations.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="e.g. CS101 Final Examination" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">— Select Type —</option>
                        @foreach($examTypes as $et)
                            <option value="{{ $et->code }}" {{ old('type') === $et->code ? 'selected' : '' }}>
                                {{ $et->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <label class="form-label">Course Offering <span class="text-danger">*</span></label>
                    <select name="course_offering_id" class="form-select @error('course_offering_id') is-invalid @enderror" required>
                        <option value="">— Select Course —</option>
                        @foreach($offerings as $off)
                            <option value="{{ $off->id }}" {{ old('course_offering_id') == $off->id ? 'selected' : '' }}>
                                {{ optional($off->course)->code }} — {{ optional($off->course)->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_offering_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Exam Date <span class="text-danger">*</span></label>
                    <input type="date" name="exam_date" class="form-control @error('exam_date') is-invalid @enderror"
                           value="{{ old('exam_date') }}" required>
                    @error('exam_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Venue</label>
                    <input type="text" name="venue" class="form-control" value="{{ old('venue') }}" placeholder="e.g. Main Hall">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Invigilator</label>
                    <select name="invigilator_id" class="form-select">
                        <option value="">— None —</option>
                        @foreach($staff as $s)
                            <option value="{{ $s->id }}" {{ old('invigilator_id') == $s->id ? 'selected' : '' }}>
                                {{ optional($s->user)->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Marks <span class="text-danger">*</span></label>
                    <input type="number" name="max_marks" class="form-control" value="{{ old('max_marks', 100) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pass Mark</label>
                    <input type="number" name="passing_marks" class="form-control" value="{{ old('passing_marks', 40) }}" min="0">
                </div>
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Schedule Examination</button>
                <a href="{{ route('academic.examinations.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
